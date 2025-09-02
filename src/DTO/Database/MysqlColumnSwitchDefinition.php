<?php

namespace Lumio\DTO\Database;

class MysqlColumnSwitchDefinition extends MysqlColumnDefinition {

    /**
     * MySQL column switch definition
     *
     *
     * @param string $name
     *
     * @return MysqlColumnSwitchDefinition
     */
    public function __construct(string $name) {

        parent::__construct(
            name: $name,
            type: self::TYPE_TINYINT,
            length: 1,
            nullable: true,
            default: '0',
            index: self::INDEX_INDEX
        );
    }

}

