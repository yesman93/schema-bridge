<?php

namespace Lumio\Traits\Database;

use Exception;
use Lumio\Database\DatabaseAdapter;
use Lumio\DTO\Database\MysqlColumnDefinition;
use Lumio\DTO\Database\MysqlColumns;
use Lumio\DTO\Database\MysqlTableDefinition;
use Lumio\Log\Logger;

trait MetadataCache {

    /**
     * Storage for all harvested metadata
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private static array $_tables = [];

    /**
     * Retrieves metadata for given table and stores it in the cache
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $name
     * @param DatabaseAdapter $adapter
     *
     * @return void
     */
    protected static function set_metadata(string $name, DatabaseAdapter $adapter) : void {

        $table_info = $adapter->row("SHOW TABLE STATUS WHERE Name = '$name'");
        if (empty($table_info)) {

            $message = 'Metadata loading failed: Table \'' . $name . '\' not found in the database.';

            if (__is_dev()) {
                throw new Exception($message);
            } else {
                Logger::channel('db')->warning($message);
                return;
            }
        }

        $table_name = $table_info['Name'];

        $columns_info = $adapter->all("SHOW FULL COLUMNS FROM `$table_name`");

        $columns_collection = new MysqlColumns();
        foreach ($columns_info as $column_info) {

            $type = $column_info['Type'] ?? '';
            $matches = [];
            preg_match('/^[a-z]+\((\d+)\)?/', $type, $matches);
            $length = $matches[1] ?? null;

            if (!empty($length)) {
                $type = preg_replace("/\($length\)$/", '', $type);
            }

            $pattern = '/^' . preg_quote($column_info['Type'] ?? '') . '/';
            $attribute = trim(preg_replace($pattern, '', $column_info['Type']));

            $column = new MysqlColumnDefinition(
                name:               $column_info['Field'] ?? '',
                type:               $type,
                length:             $length,
                collation:          $column_info['Collation'] ?? null,
                attribute:          !empty($attribute) ?: null,
                nullable:           ($column_info['Null'] ?? '') == 'YES',
                default:            $column_info['Default'] ?? null,
                auto_increment:     ($column_info['Extra'] ?? '') == 'auto_increment',
                comment:            $column_info['Comment'] ?? null,
                index:              $column_info['Key'] ?? null,
            );

            $columns_collection->add_column($column);
        }

        $table_definition = new MysqlTableDefinition(
            name: $table_name,
            columns: $columns_collection,
            engine: $table_info['Engine'] ?? '',
            collation: $table_info['Collation'] ?? '',
            comment: $table_info['Comment'] ?? '',
        );

        $model_name = strtolower($table_name);
        self::$_tables[$model_name] = $table_definition;
    }

    /**
     * Get metadata for given model
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $name
     * @param DatabaseAdapter $adapter
     *
     * @return MysqlTableDefinition
     *
     * @throws \Exception
     */
    public static function get_metadata(string $name, DatabaseAdapter $adapter): MysqlTableDefinition {

        if (!isset(self::$_tables[$name])) {

            self::set_metadata($name, $adapter);
            if (!isset(self::$_tables[$name])) {
                throw new \Exception("Metadata not found for model \"$name\"");
            }
        }

        return self::$_tables[$name];
    }

}
