<?php

namespace Lumio\Model;

use Lumio\Auth\Logged;
use Lumio\Database\DatabaseAdapter;
use Lumio\Database\TypeValidator;
use Lumio\DTO\Database\MysqlColumnDefinition;
use Lumio\DTO\Database\MysqlColumns;
use Lumio\DTO\Model\Sorting;
use Lumio\Exceptions\LumioPaginationTotalException;
use Lumio\Exceptions\LumioValidationException;
use Lumio\Exceptions\LumioDatabaseException;
use Lumio\IO\Request;
use Lumio\Encryption\Encryption;
use Lumio\Traits;
use Lumio\Utilities\Is;

abstract class BaseModel {

    use Traits\Database\MetadataCache;
    use Traits\Database\QueryBuilder;
    use Traits\Model\Pagination;
    use Traits\Model\Filter;

    /**
     * Column encryption - one way
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    const COL_ENC_ONEWAY = 'oneway';

    /**
     * Column encryption - two way
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    const COL_ENC_TWOWAY = 'twoway';

    /**
     * Column encryption - none
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    const COL_ENC_NONE = 'none';

    /**
     * instance of the database adapter
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var DatabaseAdapter
     */
    protected DatabaseAdapter $_db;

    /**
     * input data (from request passed by controller)
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    protected array $_data = [];

    /**
     * DB table name
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_table = '';

    /**
     * DB primary key
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_primary_key = '';

    /**
     * Columns of DB table
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var MysqlColumns
     */
    protected MysqlColumns $_columns;

    /**
     * Encryption types for columns
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private array $_encryptions = [];

    /**
     * Current sorting
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var Sorting|null
     */
    private ?Sorting $_sorting = null;

    /**
     * Main model
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param DatabaseAdapter $db
     *
     * @return \Lumio\Model\BaseModel
     *
     * @throws \Exception
     */
    public function __construct(DatabaseAdapter $db) {

        $this->_db = $db;

        $this->_load_metadata();
        $this->_init_pagination();
        $this->_init_search();
    }

    /**
     * Initialize search
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    private function _init_search() : void {
        $this->set_search_columns($this->_columns);
    }

    /**
     * Load metadata from cache or database
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     *
     * @throws \Exception
     */
    private function _load_metadata(): void {

        $class = static::class;
        $chunks = explode('\\', $class);
        $class = array_pop($chunks);
        $name = strtolower(str_replace('Model', '', $class));

        $metadata = self::get_metadata($name, $this->_db);

        $this->_table = $metadata->get_name();
        $this->_primary_key = $metadata->get_name() . '_id';
        $this->_columns = $metadata->get_columns();
    }

    /**
     * Get table columns
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return MysqlColumns
     */
    public function get_columns() : MysqlColumns {
        return $this->_columns;
    }

    /**
     * set input data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param array $data
     *
     * @return void
     */
    public function set_data(array $data): void {

        foreach ($this->_columns as $column) {

            $name = $column->get_name();
            if (isset($data[$name])) {
                $this->_data[$name] = $data[$name];
            }
        }
    }

    /**
     * Set encryption for given column
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string $encryption
     *
     * @return void
     */
    public function encryption(string $column, string $encryption) : void {
        $this->_encryptions[$column] = $encryption;
    }

    /**
     * Check if model has given column
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param $name
     *
     * @return bool
     */
    public function has_column($name) : bool {
        return $this->_columns->has($name);
    }

    /**
     * Validate input data (mainly for add and edit)
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param array $data
     *
     * @return void
     *
     * @throws LumioValidationException
     */
    private function _validate_input(array $data) : void {

        if (!is_nempty_array($data)) {
            return;
        }

        foreach ($this->_columns as $column) {

            $name = $column->get_name();

            if (!$column->is_nullable() && empty($data[$name] ?? '')) {
                throw new LumioValidationException(__tx('Field %s is required', $name));
            }
        }

        foreach ($data as $name => $value) {

            $column = $this->_columns->get($name);
            if (empty($column)) {
                continue;
            }

            if (!$column->is_nullable() && empty($value)) {
                throw new LumioValidationException(__tx('Field %s is required', $name));
            }

            $length = $column->get_length();
            if ($length > 0 && $length < mb_strlen($value)) {
                throw new LumioValidationException(__tx('Field %s exceeds max length %s', $name, $length));
            }

            if (!TypeValidator::validate($value, $column->get_type())) {
                throw new LumioValidationException(__tx('Field %s has invalid type', $name));
            }
        }
    }

    /**
     * Encrypt value for given column by encryption type
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param mixed $value
     *
     * @return string
     */
    private function _encrypt_value(string $column, mixed $value) : string {

        $encryption = $this->_encryptions[$column] ?? '';

        if (str_contains($column, 'password')) {

            if ($encryption == self::COL_ENC_TWOWAY) {
                $value = Encryption::encrypt($value);
            } else if ($encryption == self::COL_ENC_NONE) {
                // leave as is
            } else {
                $value = Encryption::argon2($value);
            }

        } else {

            if ($encryption == self::COL_ENC_TWOWAY) {
                $value = Encryption::encrypt($value);
            } else if ($encryption == self::COL_ENC_ONEWAY) {
                $value = Encryption::argon2($value);
            }
        }

        return $value;
    }

    /**
     * Normalize empty value for given column
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param mixed $value
     * @param MysqlColumnDefinition $column
     *
     * @return mixed
     */
    private function _normalize_empty_value(mixed $value, MysqlColumnDefinition $column) : mixed {

        if (empty($value)) {

            if ($column->is_nullable()) {
                $ret = null;
            } else {

                if ($column->get_type() == 'int') {
                    $ret = 0;
                } else if (in_array($column->get_type(), ['float', 'double', 'decimal'])) {
                    $ret = 0.0;
                } else if ($column->get_type() == 'string') {
                    $ret = '';
                } else {
                    $ret = null;
                }
            }
        }

        return $value;
    }

    /**
     * Add new record to corresponding DB table
     *
     * @return int
     *
     * @throws LumioDatabaseException
     * @throws LumioValidationException
     *@author TB
     * @date 28.4.2025
     *
     */
    public function add() : int {

        $this->before_add();

        $data = $this->_data;

        if ($this->has_column('datetime_add') && Is::empty_datetime($data['datetime_add'] ?? '')) {
            $data['datetime_add'] = get_now();
        }

        if ($this->has_column('user_id_add') && empty($data['user_id_add'])) {
            $logged = Logged::user();
            $data['user_id_add'] = $logged->get('id');
        }

        if ($this->has_column('ip_add') && empty($data['ip_add'] ?? '')) {
            $data['ip_add'] = Request::ip();
        }

        $this->_validate_input($data);

        foreach ($this->_columns as $column) {

            $name = $column->get_name();
            $value = $data[$name] ?? '';

            $data[$name] = $value = $this->_normalize_empty_value($value, $column);

            $data[$name] = $value = $this->_encrypt_value($name, $value);
        }

        try {

            $query = $this->table($this->_table)
                ->insert($data)
                ->build();

        } catch (\Exception $e) {
            throw new LumioDatabaseException(__tx('Error building SQL query for insertion: %s', $e->getMessage()));
        }

        $result = $this->_db->query(...$query);

        if (!empty($result)) {
            $result = (int) $this->_db->last_insert_id();
        } else {
            $result = 0;
        }

        $this->after_add();

        return $result;
    }

    /**
     * Edit existing record in corresponding DB table
     *
     * @param array|null $data
     *
     * @return int
     *
     * @throws LumioDatabaseException
     * @throws LumioValidationException
     * @author TB
     * @date 28.4.2025
     *
     */
    public function edit(?array $data = null) : int {

        $this->before_edit();

        if (!is_nempty_array($data)) {
            $data = $this->_data;
        }

        if (empty($data[$this->_primary_key] ?? '')) {
            throw new LumioValidationException(__tx('Value for primary key %s is required', $this->_primary_key));
        }

        $primary_key = $data[$this->_primary_key];
        unset($data[$this->_primary_key]);

        if ($this->has_column('datetime_edit') && Is::empty_datetime($data['datetime_edit'] ?? '')) {
            $data['datetime_edit'] = get_now();
        }

        if ($this->has_column('user_id_edit') && empty($data['user_id_edit'])) {
            $logged = Logged::user();
            $data['user_id_edit'] = $logged->get('id');
        }

        if ($this->has_column('ip_edit') && empty($data['ip_edit'] ?? '')) {
            $data['ip_edit'] = Request::ip();
        }

        $this->_validate_input($data);

        foreach ($this->_columns as $column) {

            $name = $column->get_name();
            $value = $data[$name] ?? '';

            $data[$name] = $value = $this->_normalize_empty_value($value, $column);

            $data[$name] = $value = $this->_encrypt_value($name, $value);
        }

        try {

            $query = $this->table($this->_table)
                ->update($data)
                ->where($this->_primary_key, '=', $primary_key)
                ->build();

        } catch (\Exception $e) {
            throw new LumioDatabaseException(__tx('Error building SQL query for update: %s', $e->getMessage()));
        }

        $result = $this->_db->query(...$query);

        if (!empty($result)) {
            $result = (int) $primary_key;
        } else {
            $result = 0;
        }

        $this->after_edit();

        return $result;
    }

    /**
     * Remove record from corresponding DB table
     *
     * @param int $id
     *
     * @return bool
     *
     * @throws LumioDatabaseException
     * @throws LumioValidationException
     * @author TB
     * @date 28.4.2025
     *
     */
    public function remove(int $id) : bool {

        $this->before_remove();

        if (empty($id)) {
            throw new LumioValidationException(__tx('No value of primary key for removal'));
        }

        try {

            $query = $this->table($this->_table)
                ->delete()
                ->where($this->_primary_key, '=', $id)
                ->build();

        } catch (\Exception $e) {
            throw new LumioDatabaseException(__tx('Error building SQL query for removal: %s', $e->getMessage()));
        }

        $result = $this->_db->query(...$query);

        $this->after_remove();

        return !empty($result);
    }

    /**
     * Before add hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function before_add() : void {

    }

    /**
     * After add hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function after_add() : void {

    }

    /**
     * Before edit hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function before_edit() : void {

    }

    /**
     * After edit hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function after_edit() : void {

    }

    /**
     * Before remove hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function before_remove() : void {

    }

    /**
     * After remove hook
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    protected function after_remove() : void {

    }

    /**
     * Get record from corresponding DB table
     *
     * @param int $id
     *
     * @return array
     *
     * @throws LumioDatabaseException
     * @throws LumioValidationException
     * @author TB
     * @date 28.4.2025
     *
     */
    public function get(int $id) : array {

        if (empty($id)) {
            throw new LumioValidationException(__tx('No value of primary key for retrieval'));
        }

        try {

            $query = $this->table($this->_table)
                ->select()
                ->where($this->_primary_key, '=', $id)
                ->build();

        } catch (\Exception $e) {
            throw new LumioDatabaseException(__tx('Error building SQL query for retrieval: %s', $e->getMessage()));
        }

        return $this->_db->row(...$query);
    }

    /**
     * Set search - where condition with search query searched in all current search columns
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return void
     */
    private function _set_search(): void {

        $search_query = $this->get_search_query();
        if ($search_query === null) {
            return;
        }

        $search_columns = $this->get_search_columns();
        if (empty($search_columns)) {
            return;
        }

        $this->where_group(function ($query) use ($search_query, $search_columns) {
            foreach ($search_columns as $column) {
                $query->where($column, 'LIKE', '%' . $search_query . '%', self::OR);
            }
        });
    }

    /**
     * Get all records from corresponding DB table
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string|array ...$columns
     *
     * @throws LumioDatabaseException
     *
     * @return array
     */
    public function all(string|array ...$columns) : array {

        try {

            $this->table($this->_table)->select(...$columns);

            $this->_set_search();

            if ($this->is_paginate()) {

                if ($this->is_pagination_get_total()) {

                    $first_column = $this->first_column_name();
                    $this->count($first_column, 'cnt', true);

                    $this->limit(null, null);

                } else {

                    [$limit, $offset] = $this->get_limit();
                    $this->limit($limit, $offset);
                }

            }

            if ($this->_sorting !== null) {
                $this->order_by($this->_sorting->get_column(), $this->_sorting->get_direction(), true);
            }

            $query = $this->build();

        } catch (\Exception $e) {
            throw new LumioDatabaseException(__tx('Error building SQL query for records retrieval: %s', $e->getMessage()));
        }

        if ($this->is_pagination_get_total()) {
            $res = $this->_db->cell(...$query);
            throw new LumioPaginationTotalException($res, $this->get_per_page()); // Throw special exception, so it can be caught in controller nicely
        }

        return $this->_db->all(...$query);
    }

    /**
     * Set sorting
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param Sorting|null $sorting
     *
     * @return void
     */
    public function sorting(?Sorting $sorting = null) : void {
        $this->_sorting = $sorting;
    }

    /**
     * Get model table name
     *
     * @author TB
     * @date 22.5.2025
     *
     * @return string
     */
    public function get_table() : string {
        return $this->_table;
    }

}
