<?php

namespace Lumio\Traits\Database;

use Lumio\Exceptions\LumioQueryException;
use Lumio\Model\BaseModel;

trait QueryBuilder {

    /**
     * Query type - SELECT
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _QT_SELECT = 'SELECT';

    /**
     * Query type - INSERT
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _QT_INSERT = 'INSERT';

    /**
     * Query type - UPDATE
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _QT_UPDATE = 'UPDATE';

    /**
     * Query type - DELETE
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _QT_DELETE = 'DELETE';

    /**
     * Join type - inner
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _JOIN_INNER = 'INNER';

    /**
     * Join type - outer
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _JOIN_OUTER = 'OUTER';

    /**
     * Join type - left
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _JOIN_LEFT = 'LEFT';

    /**
     * Join type - right
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _JOIN_RIGHT = 'RIGHT';

    /**
     * Join type - full
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _JOIN_FULL = 'FULL';

    /**
     * Operator - AND
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const AND = 'AND';

    /**
     * Operator - OR
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const OR = 'OR';

    /**
     * Sort direction - ASC
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    public const ASC = 'ASC';

    /**
     * Sort direction - DESC
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    public const DESC = 'DESC';

    /**
     * Type of WHERE condition - basic
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _WHERE_BASIC = 'basic';

    /**
     * Type of WHERE condition - group
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _WHERE_GROUP = 'group';

    /**
     * Escape character - for MySQL
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const ESCAPE_MYSQL = '`';

    /**
     * Escape character - for PostgreSQL
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const ESCAPE_PGSQL = '"';

    /**
     * Current query type
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_query_type = '';

    /**
     * Current table name
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_table_name = '';

    /**
     * Columns to select
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_select_columns = [];

    /**
     * WHERE conditions
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_where_conditions = [];

    /**
     * Parameters for the query
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_params = [];

    /**
     * JOIN clauses
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_join_clauses = [];

    /**
     * ORDER BY clauses
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_order_by = [];

    /**
     * GROUP BY clauses
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_group_by = [];

    /**
     * LIMIT clause
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private array $_limit = [null, null];

    /**
     * Data for INSERT
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_insert_data = [];

    /**
     * Data for UPDATE
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var array
     */
    private array $_update_data = [];

    /**
     * Soft reset flag
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var bool
     */
    private bool $_soft_reset = true;

    /**
     * Driver name
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_driver = 'mysql';

    /**
     * Current escape character
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_escape_char = '`';

    /**
     * Set driver and define escaping rules
     *
     * @param string $driver
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function set_driver(string $driver): self {

        $this->_driver = strtolower($driver);

        $this->_escape_char = match ($this->_driver) {
            'mysql' => self::ESCAPE_MYSQL,
            'pgsql', 'postgresql' => self::ESCAPE_PGSQL,
            default => self::ESCAPE_MYSQL
        };

        return $this;
    }

    /**
     * Set target table
     *
     * @param string $table
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function table(string $table): self {

        $this->_table = $table;

        return $this;
    }

    /**
     * Set columns to select
     *
     * @param string|array ...$columns
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function select(string|array ...$columns): self {

        $this->_query_type = self::_QT_SELECT;
        $this->_select_columns = empty($columns) ? ['*'] : $columns;

        return $this;
    }

    /**
     * Insert given data
     *
     * @param array $data
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function insert(array $data): self {

        $this->_query_type = self::_QT_INSERT;
        $this->_insert_data = $data;

        return $this;
    }

    /**
     * Update to given data
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param array $data
     *
     * @return BaseModel|QueryBuilder
     */
    public function update(array $data): self {

        $this->_query_type = self::_QT_UPDATE;
        $this->_update_data = $data;

        return $this;
    }

    /**
     * Delete from table
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function delete(): self {

        $this->_query_type = self::_QT_DELETE;

        return $this;
    }

    /**
     * Set WHERE condition
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $connector
     *
     * @return BaseModel|QueryBuilder
     */
    public function where(string $column, string $operator, mixed $value, string $connector = self::AND): self {

        $this->_where_conditions[] = [
            'type' => self::_WHERE_BASIC,
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'connector' => $connector
        ];

        return $this;
    }

    /**
     * Add nested WHERE conditions
     *
     * @param callable $callback
     * @param string $connector
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    public function where_group(callable $callback, string $connector = self::AND): self {

        $group = new class {

            use QueryBuilder;

            /**
             * WHERE conditions
             *
             * @author TB
             * @date 27.4.2025
             *
             * @var array
             */
            private array $_conditions = [];

            /**
             * Constructor
             *
             * @author TB
             * @date 27.4.2025
             *
             * @param string $column
             * @param string $operator
             * @param mixed $value
             * @param string|null $connector
             *
             * @return self
             */
            public function where(string $column, string $operator, mixed $value, ?string $connector = null) : self {

                $this->_conditions[] = [
                    'type' => self::_WHERE_BASIC,
                    'column' => $column,
                    'operator' => $operator,
                    'value' => $value,
                    'connector' => $connector ?? self::AND,
                ];

                return $this;
            }

            /**
             * Returns conditions
             *
             * @author TB
             * @date 27.4.2025
             *
             * @return array
             */
            public function get_conditions(): array {
                return $this->_conditions;
            }

        };

        $callback($group);

        $this->_where_conditions[] = [
            'type' => self::_WHERE_GROUP,
            'conditions' => $group->get_conditions(),
            'connector' => $connector,
        ];

        return $this;
    }

    /**
     * Add JOIN clause
     *
     * @param string $table
     * @param array $conditions
     * @param string $type
     * @param string|null $alias
     * @param string $connector
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return BaseModel|QueryBuilder
     */
    private function _join(
        string $table,
        array $conditions,
        string $type = self::_JOIN_INNER,
        ?string $alias = null,
        string $connector = self::AND
    ): self {

        $alias = $alias ?? $this->_generate_alias($table);

        $this->_join_clauses[] = [
            'table' => $table,
            'alias' => $alias,
            'conditions' => $conditions,
            'type' => $type,
            'connector' => $connector,
        ];

        return $this;
    }

    /**
     * Add INNER JOIN clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     * @param array $conditions
     * @param string|null $alias
     *
     * @return BaseModel|QueryBuilder
     */
    public function join(string $table, array $conditions, ?string $alias = null): self {
        return $this->_join($table, $conditions, self::_JOIN_INNER, $alias);
    }

    /**
     * Add OUTER JOIN clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     * @param array $conditions
     * @param string|null $alias
     *
     * @return BaseModel|QueryBuilder
     */
    public function outer_join(string $table, array $conditions, ?string $alias = null): self {
        return $this->_join($table, $conditions, self::_JOIN_OUTER, $alias);
    }

    /**
     * Add LEFT JOIN clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     * @param array $conditions
     * @param string|null $alias
     *
     * @return BaseModel|QueryBuilder
     */
    public function left_join(string $table, array $conditions, ?string $alias = null): self {
        return $this->_join($table, $conditions, self::_JOIN_LEFT, $alias);
    }

    /**
     * Add RIGHT JOIN clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     * @param array $conditions
     * @param string|null $alias
     *
     * @return BaseModel|QueryBuilder
     */
    public function right_join(string $table, array $conditions, ?string $alias = null): self {
        return $this->_join($table, $conditions, self::_JOIN_RIGHT, $alias);
    }

    /**
     * Add FULL JOIN clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     * @param array $conditions
     * @param string|null $alias
     *
     * @return BaseModel|QueryBuilder
     */
    public function full_join(string $table, array $conditions, ?string $alias = null): self {
        return $this->_join($table, $conditions, self::_JOIN_FULL, $alias);
    }

    /**
     * Add ORDER BY clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $column
     * @param string $direction
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function order_by(string $column, string $direction = self::ASC, bool $only = false): self {

        if ($only) {
            $this->_order_by = [];
        }

        $this->_order_by[] = [$column, strtoupper($direction)];

        return $this;
    }

    /**
     * Add GROUP BY clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string ...$columns
     *
     * @return BaseModel|QueryBuilder
     */
    public function group_by(string ...$columns): self {

        foreach ($columns as $col) {
            $this->_group_by[] = $col;
        }

        return $this;
    }

    /**
     * Add LIMIT clause
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return BaseModel|QueryBuilder
     */
    public function limit(?int $limit, ?int $offset = null) : self {

        $this->_limit = [];

        $this->_limit[] = $limit;
        $this->_limit[] = $offset;

        return $this;
    }

    /**
     * Build the query
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return array
     *
     * @throws \Exception
     */
    public function build(): array {

        return match ($this->_query_type) {
            self::_QT_SELECT => $this->_build_select(),
            self::_QT_INSERT => $this->_build_insert(),
            self::_QT_UPDATE => $this->_build_update(),
            self::_QT_DELETE => $this->_build_delete(),
            default => throw new \Exception('Unknown query type'),
        };
    }

    /**
     * Build SELECT query.
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return array
     */
    private function _build_select(): array {

        $query = '
        SELECT ';

        if (empty($this->_select_columns)) {
            $query .= '*';
        } else {

            $query .= implode(', ', array_map( function ($col) {

                if (is_array($col)) {
                    return $this->_escape_name($col[0]) . ' AS ' . $this->_escape_name($col[1]);
                } else {
                    return $this->_escape_name($col);
                }

            }, $this->_select_columns));
        }

        $query .= ' 
        FROM ' . $this->_escape_name($this->_table);

        foreach ($this->_join_clauses as $join) {

            $query .= ' 
        ' . $join['type'] . ' JOIN ' . $this->_escape_name($join['table']) . ' AS ' . $this->_escape_name($join['alias']) . ' ON ';

            $query .= implode(' ' . $join['connector'] . ' ', $join['conditions']);


        }

        $params = [];
        if (!empty($this->_where_conditions)) {
            [$where_sql, $params] = $this->_build_where($this->_where_conditions);
            $query .= ' 
        WHERE ' . $where_sql;
        }

        if (!empty($this->_group_by)) {
            $query .= ' 
        GROUP BY ' . implode(', ', array_map([$this, '_escape_name'], $this->_group_by));
        }

        if (!empty($this->_order_by)) {
            $order = array_map(fn($o) => $this->_escape_name($o[0]) . ' ' . $o[1], $this->_order_by);
            $query .= ' 
        ORDER BY ' . implode(', ', $order);
        }

        if (!empty($this->_limit[0])) {

            $query .= '
        LIMIT ' . $this->_limit[0];

            if (!empty($this->_limit[1])) {
                $query .= ' OFFSET ' . $this->_limit[1];
            }
        }

        $this->_reset();

        return [$query, $params];
    }

    /**
     * Build INSERT query
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return array
     */
    private function _build_insert(): array {

        $query = 'INSERT INTO ' . $this->_escape_name($this->_table);

        $columns = array_keys($this->_insert_data[0] ?? $this->_insert_data);

        $query .= ' (' . implode(', ', array_map([$this, '_escape_name'], $columns)) . ') VALUES ';

        if (isset($this->_insert_data[0]) && is_array($this->_insert_data[0])) {

            $placeholders = '(' . rtrim(str_repeat('?, ', count($columns)), ', ') . ')';

            $query .= implode(', ', array_fill(0, count($this->_insert_data), $placeholders));
            $params = array_merge(...array_map('array_values', $this->_insert_data));

        } else {
            $query .= '(' . rtrim(str_repeat('?, ', count($columns)), ', ') . ')';
            $params = array_values($this->_insert_data);
        }

        $this->_reset();

        return [$query, $params];
    }

    /**
     * Build UPDATE query
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return array
     */
    private function _build_update(): array {

        $query = 'UPDATE ' . $this->_escape_name($this->_table) . ' SET ';

        $query .= implode(', ', array_map(
            fn ($col) => $this->_escape_name($col) . ' = ?',
            array_keys($this->_update_data)
        ));

        $params = array_values($this->_update_data);

        if (!empty($this->_where_conditions)) {

            [$where_sql, $where_params] = $this->_build_where($this->_where_conditions);

            $query .= ' WHERE ' . $where_sql;
            $params = array_merge($params, $where_params);
        }

        $this->_reset();

        return [$query, $params];
    }

    /**
     * Build DELETE query
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return array
     */
    private function _build_delete(): array {

        $query = 'DELETE FROM ' . $this->_escape_name($this->_table);

        $params = [];
        if (!empty($this->_where_conditions)) {

            [$where_sql, $where_params] = $this->_build_where($this->_where_conditions);

            $query .= ' WHERE ' . $where_sql;
            $params = array_merge($params, $where_params);
        }

        $this->_reset();

        return [$query, $params];
    }

    /**
     * Escape names depending on driver
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $name
     *
     * @return string
     */
    private function _escape_name(string $name): string {

        $aggregate_functions = ['count', 'sum', 'avg', 'max', 'min'];
        foreach ($aggregate_functions as $func) {

            if (str_starts_with($name, $func . '(') || str_starts_with($name, $func . ' (')) {
                return $name;
            }
        }

        if (strpos($name, '.') !== false) {

            return implode('.', array_map(
                fn($part) => $part == '*' ? $part : $this->_escape_char . $part . $this->_escape_char,
                explode('.', $name)
            ));
        }

        return $name == '*' ? $name : $this->_escape_char . $name . $this->_escape_char;
    }

    /**
     * Generate a table alias
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $table
     *
     * @return string
     */
    private function _generate_alias(string $table): string {

        $parts = explode('_', $table);

        $alias = '';
        foreach ($parts as $i => $p) {

            if ($i === 0) {
                $alias .= strtolower($p[0]);
            } else {
                $alias .= strtolower(substr($p, 0, 2));
            }
        }

        return $alias;
    }

    /**
     * Build WHERE clause
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param array $conditions
     *
     * @return array
     */
    private function _build_where(array $conditions): array {

        $sql = '';
        $params = [];
        foreach ($conditions as $i => $cond) {

            if ($i > 0) {
                $sql .= ' ' . $cond['connector'] . ' ';
            }

            if ($cond['type'] === self::_WHERE_GROUP) {

                [$sql_clause, $params_clause] = $this->_build_where($cond['conditions']);

                $sql .= '(' . $sql_clause . ')';
                $params = array_merge($params, $params_clause);

            } else {
                $sql .= $this->_escape_name($cond['column']) . ' ' . $cond['operator'] . ' ?';
                $params[] = $cond['value'];
            }
        }

        return [$sql, $params];
    }

    /**
     * Get the first column of the current select
     * If select is '*', get first real column of the table that its selecting from
     *
     * @author TB 28.4.2025
     * @return string
     *
     * @throws LumioQueryException
     */
    public function first_column_name(): string {

        if (empty($this->_select_columns)) {
            throw new LumioQueryException('No columns selected');
        }

        if ($this->_select_columns === ['*']) {

            if (empty($this->_columns)) {
                throw new LumioQueryException('Cannot get first column, metadata not available');
            }

            return $this->_columns->first_column_name();
        }

        if (is_array($this->_select_columns[0])) {
            return $this->_select_columns[0][0];
        } else {
            return $this->_select_columns[0];
        }
    }

    /**
     * Add aggregate function COUNT() to the select
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string|null $alias
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function count(string $column = '*', ?string $alias = null, bool $only = false) : self {

        $column = 'count(' . $this->_escape_name($column) . ')';

        if ($only) {
            $this->_select_columns = [];
        }

        $this->_select_columns[] = $alias === null ? $column : [$column, $alias];

        return $this;
    }

    /**
     * Add aggregate function SUM() to the select
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string|null $alias
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function sum(string $column, ?string $alias = null, bool $only = false) : self {

        $column = 'sum(' . $this->_escape_name($column) . ')';

        if ($only) {
            $this->_select_columns = [];
        }

        $this->_select_columns[] = $alias === null ? $column : [$column, $alias];

        return $this;
    }

    /**
     * Add aggregate function AVG() to the select
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string|null $alias
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function avg(string $column, ?string $alias = null, bool $only = false) : self {

        $column = 'avg(' . $this->_escape_name($column) . ')';

        if ($only) {
            $this->_select_columns = [];
        }

        $this->_select_columns[] = $alias === null ? $column : [$column, $alias];

        return $this;
    }

    /**
     * Add aggregate function MAX() to the select
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string|null $alias
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function max(string $column, ?string $alias = null, bool $only = false) : self {

        $column = 'max(' . $this->_escape_name($column) . ')';

        if ($only) {
            $this->_select_columns = [];
        }

        $this->_select_columns[] = $alias === null ? $column : [$column, $alias];

        return $this;
    }

    /**
     * Add aggregate function MIN() to the select
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $column
     * @param string|null $alias
     * @param bool $only
     *
     * @return BaseModel|QueryBuilder
     */
    public function min(string $column, ?string $alias = null, bool $only = false) : self {

        $column = 'min(' . $this->_escape_name($column) . ')';

        if ($only) {
            $this->_select_columns = [];
        }

        $this->_select_columns[] = $alias === null ? $column : [$column, $alias];

        return $this;
    }

    /**
     * Reset all properties after build
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return void
     */
    private function _reset(): void {

        $this->_query_type = '';
        $this->_select_columns = [];
        $this->_where_conditions = [];
        $this->_join_clauses = [];
        $this->_order_by = [];
        $this->_group_by = [];
        $this->_insert_data = [];
        $this->_update_data = [];
        $this->_soft_reset = true;
    }

}
