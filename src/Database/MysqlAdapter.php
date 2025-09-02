<?php

namespace Lumio\Database;

use Lumio\DTO\Database\DatabaseCredentials;
use PDO;
use PDOException;

class MysqlAdapter extends DatabaseAdapter {

    /**
     * MySQL database connection
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var PDO
     */
    private PDO $_pdo;

    /**
     * MySQL database adapter
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param DatabaseCredentials $credentials
     *
     * @return \Lumio\Database\MysqlAdapter
     */
    public function __construct(DatabaseCredentials $credentials) {

        $dsn = 'mysql:host=' . $credentials->host() . ';dbname=' . $credentials->dbname() . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->_pdo = new PDO($dsn, $credentials->username(), $credentials->password(), $options);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

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
    public function query(string $query, array $params = []) : bool {

        $stmt = $this->_pdo->prepare($query);

        return  $stmt->execute($params);
    }

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
    public function all(string $query, array $params = []) : array {

        $stmt = $this->_pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

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
    public function row(string $query, array $params = []) : array {

        $stmt = $this->_pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch() ?: [];
    }

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
    public function column(string $query, array $params = []) : array {

        $stmt = $this->_pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Fetch the first column of first row
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param string $query
     * @param array $params
     *
     * @return string
     */
    public function cell(string $query, array $params = []) : string {

        $stmt = $this->_pdo->prepare($query);
        $stmt->execute($params);

        $result = $stmt->fetchColumn();

        return !empty($result) ? $result : '';
    }

    /**
     * Get last inserted ID
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function last_insert_id() : string {
        return $this->_pdo->lastInsertId();
    }

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
    public function get_tables(bool $extended = false): array {

        $tables = $this->all($extended ? "SHOW TABLE STATUS" : "SHOW TABLES");
        if (empty($tables)) {
            return [];
        }

        $result = [];

        if (!$extended) {

            foreach ($tables as $table) {

                if (is_array($table)) foreach ($table as $t) {
                    $result[$t] = $t;
                } else {
                    $result[$table] = $table;
                }
            }

        } else {

            foreach ($tables as $table) {

                $result[$table['Name']] = [
                    'name'      => $table['Name'],
                    'columns'   => $this->all("SHOW FULL COLUMNS FROM `$table[Name]`"),
                    'keys'      => $this->all("SHOW KEYS FROM `$table[Name]`"),
                    'engine'    => $table['Engine'] ?? null,
                    'collation' => $table['Collation'] ?? null,
                ];
            }
        }

        return $result;
    }

}
