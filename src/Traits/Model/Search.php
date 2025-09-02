<?php

namespace Lumio\Traits\Model;

use Lumio\DTO\Database\MysqlColumns;

trait Search {

    /**
     * Current search query string
     *
     *
     * @var string|null
     */
    protected ?string $_search_query = null;

    /**
     * Columns to search in
     *
     *
     * @var array
     */
    protected array $_search_columns = [];

    /**
     * Set the search query
     *
     *
     * @param string|null $query
     *
     * @return void
     */
    public function set_search_query(?string $query): void {
        $this->_search_query = $query;
    }

    /**
     * Get the search query
     *
     *
     * @return string|null
     */
    public function get_search_query(): ?string {
        return $this->_search_query;
    }

    /**
     * Set search columns by given database columns
     *
     *
     * @param MySQLColumns $columns
     *
     * @return void
     */
    public function set_search_columns(MysqlColumns $columns): void {

        $this->_search_columns = [];

        foreach ($columns as $column) {
            $this->_search_columns[] = $column->get_name();
        }
    }

    /**
     * Add search column
     *
     *
     * @param string $column
     *
     * @return void
     */
    public function search_column($column): void {

        if (!in_array($column, $this->_search_columns)) {
            $this->_search_columns[] = $column;
        }
    }

    /**
     * Remove one or more column names from the search list.
     *
     *
     * @param string $column
     *
     * @return void
     */
    public function remove_search_column(string $column): void {

        if (($key = array_search($column, $this->_search_columns, true)) !== false) {
            unset($this->_search_columns[$key]);
        }
    }

    /**
     * Overwrite all search columns with given set
     *
     *
     * @param array $columns
     *
     * @return void
     */
    public function replace_search_columns(array $columns): void {
        $this->_search_columns = $columns;
    }

    /**
     * Get current search columns
     *
     *
     * @return array
     */
    public function get_search_columns(): array {
        return $this->_search_columns;
    }

}
