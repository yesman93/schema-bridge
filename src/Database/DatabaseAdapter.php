<?php

namespace Lumio\Database;

abstract class DatabaseAdapter {

    /**
     * Execute a query with optional parameters
     *
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
     *
     * @param bool $extended
     *
     * @return array
     */
    abstract public function get_tables(bool $extended = false): array;

}
