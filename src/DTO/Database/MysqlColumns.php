<?php

namespace Lumio\DTO\Database;

class MysqlColumns implements \IteratorAggregate {

    /**
     * Columns
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var MysqlColumnDefinition[]
     */
    private array $_columns = [];

    /**
     * Adds a column to the collection
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param MysqlColumnDefinition $column
     *
     * @return void
     */
    public function add_column(MysqlColumnDefinition $column): void {
        $this->_columns[$column->get_name()] = $column;
    }

    /**
     * Get all columns
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return MysqlColumnDefinition[]
     */
    public function get_columns(): array {
        return $this->_columns;
    }

    /**
     * Get an iterator for the columns
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->_columns);
    }

    /**
     * Generates the SQL for the columns
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return string
     */
    public function to_sql(): string {

        $column_definitions = [];
        $index_definitions = [];

        foreach ($this->_columns as $column) {

            $column_definitions[] = $column->to_sql();

            if ($column->get_index() === MysqlColumnDefinition::INDEX_PRIMARY) {
                $index_definitions[] = "PRIMARY KEY (`{$column->get_name()}`)";
            } elseif ($column->get_index() === MysqlColumnDefinition::INDEX_INDEX) {
                $index_definitions[] = "INDEX (`{$column->get_name()}`)";
            }
        }

        return implode(",\n", array_merge($column_definitions, $index_definitions));
    }

    /**
     * Check if a column exists in the collection
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool {
        return isset($this->_columns[$name]);
    }

    /**
     * Get a column by name
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $name
     *
     * @return MysqlColumnDefinition|null
     */
    public function get(string $name) : ?MysqlColumnDefinition {

        if ($this->has($name)) {
            return $this->_columns[$name];
        }

        return null;
    }

    /**
     * Get the first column name
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return string
     */
    public function first_column_name() : string {
        return array_key_first($this->_columns) ?: '';
    }

}

