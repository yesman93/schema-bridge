<?php

namespace Lumio\Database\Scaffoldr;

use Exception;
use Lumio\Config;
use Lumio\Container;
use Lumio\Database\DatabaseAdapter;
use Lumio\Log\Logger;
use Spyc;

class Scaffoldr {

    /**
     * Instance of database adapter
     *
     *
     * @var DatabaseAdapter
     */
    private DatabaseAdapter $_adapter;

    /**
     * Tables in the database
     *
     *
     * @var array
     */
    private array $_tables = [];

    /**
     * Scaffoldr - Tool for synchronizing database schema with YAML definitions
     *
     * Initializes database adapter and retrieves list of tables
     *
     *
     * @param Container $container
     *
     * @throws Exception
     */
    public function __construct(Container $container) {

        $this->_adapter = $container->get(DatabaseAdapter::class);
        $this->_tables = $this->_adapter->get_tables(true);
    }

    /**
     * Synchronizes the database schema with the YAML files in the schema directory
     *
     *
     * @return void
     *
     * @throws Exception
     */
    public function sync(): void {

        $dir = Config::get('scaffoldr.schema_path');

        $files = glob($dir . '/*.yaml');
        if (!empty($files)) foreach ($files as $file) {
            $this->sync_table_from_file($file);
        }
    }

    /**
     * Synchronizes a single table from given YAML file
     *
     *
     * @param string $filepath Full path to the YAML file
     *
     * @return void
     *
     * @throws Exception
     */
    private function sync_table_from_file(string $filepath): void {

        if (empty($filepath) || !file_exists($filepath) || !is_file($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'yaml') {
            Logger::channel('scaffoldr')->error('YAML file does not exist: ' . $filepath);
            return;
        }

        $table_definition = Yaml2definition::load($filepath);
        $table_name = basename($filepath, '.yaml');

        if (!isset($this->_tables[$table_name])) {

            $sql = $table_definition->to_sql();

            try {
                $this->_adapter->query($sql);
                Logger::channel('scaffoldr')->info('Created table ' . $table_name, ['sql' => $sql]);
            } catch (\PDOException $e) {
                Logger::channel('scaffoldr')->error('Unable to create table ' . $table_name . '. Error: ' . $e->getMessage(), ['sql' => $sql]);
            } catch (\Throwable $e) {
                Logger::channel('scaffoldr')->error('Unexpected error while creating table ' . $table_name . '. Error: ' . $e->getMessage(), ['sql' => $sql]);
            }

        } else {

            $differ = TableDiffer::determine($table_name, $this->_tables[$table_name], $table_definition);
            if ($differ->has_differences()) {

                $sql = $differ->get_alter_sql();

                try {
                    $this->_adapter->query($sql);
                    Logger::channel('scaffoldr')->info('Altered table ' . $table_name, ['sql' => $sql, 'differences' => $differ->get_differences()]);
                } catch (\PDOException $e) {
                    Logger::channel('scaffoldr')->error('Unable to alter table ' . $table_name . '. Error: ' . $e->getMessage(), ['sql' => $sql]);
                } catch (\Throwable $e) {
                    Logger::channel('scaffoldr')->error('Unexpected error while altering table ' . $table_name . '. Error: ' . $e->getMessage(), ['sql' => $sql]);
                }
            }
        }
    }

}
