<?php

namespace Lumio\Database;

abstract class DatabaseAdapter {

    /**
     * Execute a query with optional parameters
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return bool
     */
    abstract public function query(string $query, array $params = []): bool;

    /**
     * Fetch all results
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return array
     */
    abstract public function all(string $query, array $params = []): array;

    /**
     * Fetch first column
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return array
     */
    abstract public function column(string $query, array $params = []): array;

    /**
     * Fetch first row
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return array
     */
    abstract public function row(string $query, array $params = []): array;

    /**
     * Fetch single cell
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return string
     */
    abstract public function cell(string $query, array $params = []): string;

    /**
     * Get last inserted ID
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    abstract public function last_insert_id(): string;

    /**
     * Get table structure from database
     *
     * Returns an associative array keyed by table name.
     * Each value is an array with a key 'columns' => [ ... column data ... ]
     *
     * @author TB
     * @date 23.5.2025
     *
     * @param bool $extended
     *
     * @return array
     */
    abstract public function get_tables(bool $extended = false): array;

}
