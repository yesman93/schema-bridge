<?php

namespace Lumio\Database\Scaffoldr;

use InvalidArgumentException;
use Lumio\DTO\Database\MysqlColumnDefinition;
use Lumio\DTO\Database\MysqlColumns;
use Lumio\DTO\Database\MysqlIndexDefinition;
use Lumio\DTO\Database\MysqlIndexes;
use Lumio\DTO\Database\MysqlTableDefinition;
use Spyc;

class Yaml2definition {

    /**
     * Loads a MySQL table definition from given YAML file
     *
     *
     * @param string $filepath
     *
     * @return MysqlTableDefinition
     *
     * @throws InvalidArgumentException
     */
    public static function load(string $filepath): MysqlTableDefinition {

        $schema = Spyc::YAMLLoad($filepath);

        if (!is_array($schema) || !isset($schema['table']) || !isset($schema['columns']) || !is_array($schema['columns'])) {
            throw new InvalidArgumentException("Invalid schema format in YAML file: {$filepath}");
        }

        $columns = new MysqlColumns();
        foreach ($schema['columns'] as $name => $col) {

            $columns->add_column(new MysqlColumnDefinition(
                name: $col['name'],
                type: $col['type'],
                length: $col['length'] ?? null,
                collation: $col['collation'] ?? null,
                nullable: $col['nullable'] ?? false,
                default: $col['default'] ?? null,
                auto_increment: $col['auto_increment'] ?? false,
                comment: $col['comment'] ?? '',
                index: $col['index'] ?? null
            ));
        }

        $indexes = null;
        if (isset($schema['indexes']) && is_array($schema['indexes'])) {

            $indexes = new MysqlIndexes();
            foreach ($schema['indexes'] as $index) {

                if (!isset($index['columns']) || !isset($index['type'])) {
                    continue;
                }

                $indexes->add_index(new MysqlIndexDefinition($index['type'], $index['columns'], $index['name'] ?? null));
            }
        }

        return new MysqlTableDefinition(
            name: $schema['table'],
            columns: $columns,
            indexes: $indexes,
            engine: $schema['engine'],
            collation: $schema['collation']
        );
    }

}
