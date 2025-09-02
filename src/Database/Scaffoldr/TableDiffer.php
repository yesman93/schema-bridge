<?php

namespace Lumio\Database\Scaffoldr;

use Lumio\DTO\Database\MysqlIndexDefinition;
use Lumio\DTO\Database\MysqlTableDefinition;
use Lumio\DTO\Database\MysqlColumnDefinition;
use Lumio\Traits;

class TableDiffer {

    use Traits\Database\Index;

    /**
     * Type of difference - add new column
     *
     *
     * @var string
     */
    private const string _TYPE_ADD_COLUMN = 'ADD_COLUMN';

    /**
     * Type of difference - change existing column
     *
     *
     * @var string
     */
    private const string _TYPE_CHANGE_COLUMN = 'CHANGE_COLUMN';

    /**
     * Type of difference - add new index
     *
     *
     * @var string
     */
    private const string _TYPE_ADD_INDEX = 'ADD_INDEX';

    /**
     * Type of difference - drop existing index
     *
     *
     * @var string
     */
    private const string _TYPE_DROP_INDEX = 'DROP_INDEX';

    /**
     * Database table name
     *
     *
     * @var string
     */
    private string $_table_name;

    /**
     * Table structure from the database
     *
     *
     * @var array
     */
    private array $_db_structure;

    /**
     * Schema definition for the table
     *
     *
     * @var MysqlTableDefinition
     */
    private MysqlTableDefinition $_schema;

    /**
     * Existing columns in the table
     *
     *
     * @var array
     */
    private array $_columns = [];

    /**
     * Existing indexes in the table
     *
     *
     * @var array
     */
    private array $_indexes = [];

    /**
     * Differences between the database structure and the schema definition
     *
     *
     * @var array
     */
    private array $_differences = [];

    /**
     * Database table differences
     *
     * This class compares the structure of a MySQL table in the database with a defined schema and identifies differences.
     * It can be used to generate SQL statements for altering the table structure to match the schema definition.
     *
     *
     * @param string $table_name
     * @param array $db_structure
     * @param MysqlTableDefinition $_schema
     *
     * @return void
     */
    public function __construct(string $table_name, array $db_structure, MysqlTableDefinition $_schema) {

        $this->_table_name = $table_name;
        $this->_db_structure = $db_structure;
        $this->_schema = $_schema;

        $this->_normalize();
    }

    /**
     * Normalizes the database structure to a consistent format
     *
     * This method processes the database structure to create a normalized representation of columns and indexes,
     * which can be easily compared with the schema definition.
     *
     *
     * @return void
     */
    private function _normalize() {

        // Columns
        $this->_columns = [];
        foreach ($this->_db_structure['columns'] as $col) {
            $this->_columns[$col['Field']] = $col;
        }

        // Indexes
        $this->_indexes = [];
        if (!empty($this->_db_structure['keys'])) {

            $normalized = [];
            foreach ($this->_db_structure['keys'] as $index) {

                $name = strtolower($index['Key_name'] ?? '');

                $type = self::INDEX_INDEX;
                if ($name === 'primary') {
                    $type = self::INDEX_PRIMARY;
                } else if ((int)$index['Non_unique'] === 0) {
                    $type = self::INDEX_UNIQUE;
                }

                $type = strtolower($type);

                if (!isset($normalized[$name . '-' . $type])) {

                    $normalized[$name . '-' . $type] = [];
                    $normalized[$name . '-' . $type]['type'] = $type;
                    $normalized[$name . '-' . $type]['name'] = $name;
                    $normalized[$name . '-' . $type]['columns'] = [];
                }

                $normalized[$name . '-' . $type]['columns'][] = $index['Column_name'];
            }

            foreach ($normalized as $index) {
                $key = $index['type'] . '-' . $index['name'] . '-' . implode('-', $index['columns']);
                $this->_indexes[$key] = $index;
            }
        }

    }

    /**
     * Factory method to create an instance of TableDiffer and determine differences
     *
     *
     * @param string $table_name
     * @param array $db_structure
     * @param MysqlTableDefinition $schema_definition
     *
     * @return TableDiffer
     */
    public static function determine(string $table_name, array $db_structure, MysqlTableDefinition $schema_definition): self {

        $instance = new self($table_name, $db_structure, $schema_definition);

        $instance->determine_differences();

        return $instance;
    }

    /**
     * Compares columns in the database with those defined in the schema
     *
     *
     * @return void
     */
    private function _compare_columns(): void {

        foreach ($this->_schema->get_columns() as $column) {

            $name = $column->get_name();

            if (!isset($this->_columns[$name])) {

                $this->_differences[] = [
                    'type' => self::_TYPE_ADD_COLUMN,
                    'column' => $column
                ];

                continue;
            }

            $db_col = $this->_columns[$name];
            $diff = [];

            // Data type
            if (strtolower($db_col['Type']) !== strtolower($column->get_type())) {

                $type_clean = preg_replace('/\(\d+\)/', '', strtolower($db_col['Type']));
                if ($type_clean !== strtolower($column->get_type())) {

                    $diff['type'] = [
                        'from' => $db_col['Type'],
                        'to' => $column->get_type()
                    ];
                }
            }

            // Length
            if (!is_null($column->get_length()) && strpos($db_col['Type'], '(' . $column->get_length() . ')') === false) {

                $matches = [];
                preg_match('/\((\d+)\)/', $db_col['Type'], $matches);
                $db_length = isset($matches[1]) ? (int)$matches[1] : null;

                $diff['length'] = [
                    'from' => $db_length,
                    'to' => $column->get_length()
                ];
            }

            // Nullability
            $db_nullable = $db_col['Null'] === 'YES';
            if ($db_nullable !== $column->is_nullable()) {

                $diff['nullable'] = [
                    'from' => $db_col['Null'],
                    'to' => $column->is_nullable() ? 'YES' : 'NO'
                ];
            }

            // Default value
            if ($db_col['Default'] != $column->get_default()) {

                $diff['default'] = [
                    'from' => $db_col['Default'],
                    'to' => $column->get_default()
                ];
            }

            // Auto increment
            $db_ai = strpos($db_col['Extra'], 'auto_increment') !== false;
            if ($db_ai !== $column->is_auto_increment()) {

                $diff['auto_increment'] = [
                    'from' => $db_ai,
                    'to' => $column->is_auto_increment()
                ];
            }

            // Collation
            if ($db_col['Collation'] !== $column->get_collation()) {

                $diff['collation'] = [
                    'from' => $db_col['Collation'],
                    'to' => $column->get_collation()
                ];
            }

            // Comment
            if (($db_col['Comment'] ?? '') !== ($column->get_comment() ?? '')) {

                $diff['comment'] = [
                    'from' => $db_col['Comment'],
                    'to' => $column->get_comment()
                ];
            }

            // Index
//            $index_map = [ 'PRI' => 'primary', 'MUL' => 'index', '' => null ];
//            $db_index = $index_map[$db_col['Key']] ?? null;
//            $expected_index = $column->get_index();
//            if (strtolower($db_index ?? '') !== strtolower($expected_index ?? '')) {
//
//                $diff['index'] = [
//                    'from' => $db_index,
//                    'to' => $expected_index
//                ];
//            }



            if (!empty($diff)) {

                $this->_differences[] = [
                    'type' => self::_TYPE_CHANGE_COLUMN,
                    'column' => $column,
                    'differences' => $diff
                ];
            }
        }
    }

    /**
     * Compares indexes in the database with those defined in the schema
     *
     * This method checks for indexes that need to be added or dropped based on the schema definition
     *
     *
     * @return void
     */
    private function _compare_indexes(): void {

        foreach ($this->_schema->get_indexes() as $index) {

            $key = $index->get_comparison_key();
            if (isset($this->_indexes[$key])) {
                unset($this->_indexes[$key]);
            } else {

                $this->_differences[] = [
                    'type' => self::_TYPE_ADD_INDEX,
                    'index' => $index
                ];
            }
        }

        if ($this->_indexes !== []) foreach ($this->_indexes as $key => $index) {

            [$type, $name, $columns] = explode('-', $key, 3);

            $this->_differences[] = [
                'type' => self::_TYPE_DROP_INDEX,
                'index' => new MysqlIndexDefinition($type, explode('-', $columns), $name),
            ];
        }
    }

    /**
     * Determines differences between database structure and schema definition
     *
     * This method compares the columns in the database with those defined in the schema and identifies any additions or changes needed
     *
     *
     * @return void
     */
    public function determine_differences(): void {

        $this->_compare_columns();

        $this->_compare_indexes();
    }

    /**
     * Checks if there are any differences between the database structure and the schema definition
     *
     *
     * @return bool
     */
    public function has_differences(): bool {
        return !empty($this->_differences);
    }

    /**
     * Returns the differences found between the database structure and the schema definition
     *
     *
     * @return array
     */
    public function get_differences(): array {
        return $this->_differences;
    }

    /**
     * Generates the SQL statement to alter the table based on the differences found
     *
     *
     * @return string
     */
    public function get_alter_sql(): string {

        if (empty($this->_differences)) {
            return '';
        }

        $clauses = [];
        foreach ($this->_differences as $diff) {

            if (isset($diff['column'])) {

                if ($diff['type'] === self::_TYPE_ADD_COLUMN) {
                    $clauses[] = 'ADD COLUMN ' . $diff['column']->to_sql();
                } else if ($diff['type'] === self::_TYPE_CHANGE_COLUMN) {
                    $clauses[] = 'MODIFY COLUMN ' . $diff['column']->to_sql();
                }

            } else if (isset($diff['index'])) {

                if ($diff['type'] === self::_TYPE_ADD_INDEX) {
                    $clauses[] = 'ADD ' . $diff['index']->to_sql();
                } else if ($diff['type'] === self::_TYPE_DROP_INDEX) {

                    if ($diff['index']->get_type() == self::INDEX_PRIMARY) {
                        $clauses[] = 'DROP PRIMARY KEY';
                    } else {
                        $clauses[] = 'DROP INDEX `' . $diff['index']->get_name() . '`';
                    }
                }
            }
        }

        return 'ALTER TABLE `' . $this->_table_name . '` ' . implode(",\n  ", $clauses) . ';';
    }

}
