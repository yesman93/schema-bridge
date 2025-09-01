<?php

namespace SchemaBridge\Db;

use PDO;
use SchemaBridge\Bootstrap;

class Connection
{

    /**
     * Create and return a PDO instance using config/database.php
     *
     * @return PDO
     */
    public static function make(): PDO
    {

        $cfg = Bootstrap::config('database');

        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $cfg['driver'],
            $cfg['host'],
            $cfg['database'],
            $cfg['charset']
        );

        $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }
}
