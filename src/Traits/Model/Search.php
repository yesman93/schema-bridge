<?php

namespace Lumio\Traits\Model;

use Lumio\DTO\Database\MysqlColumns;

trait Search {

    /**
     * Current search query string
     *
     * @author TB
     * @date 18.5.2025
     *
     * @var string|null
     */
    protected ?string $_search_query = null;

    /**
     * Columns to search in
     *
     * @author TB
     * @date 18.5.2025
     *
     * @var array
     */
    protected array $_search_columns = [];

    /**
     * Set the search query
     *
     * @author TB
     * @date 18.5.2025
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
     * @author TB
     * @date 18.5.2025
     *
     * @return string|null
     */
    public function get_search_query(): ?string {
        return $this->_search_query;
    }

    /**
     * Set search columns by given database columns
     *
     * @author TB
     * @date 18.5.2025
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
     * @author TB
     * @date 18.5.2025
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
     * @author TB
     * @date 18.5.2025
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
     * @author TB
     * @date 18.5.2025
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
     * @author TB
     * @date 18.5.2025
     *
     * @return array
     */
    public function get_search_columns(): array {
        return $this->_search_columns;
    }

}
