<?php

namespace Lumio\Contract;

use Lumio\DTO\Database\MysqlTableDefinition;

/**
 * Contract for any model or class that can define a database table schema
 *
 */
interface DBDefinitionContract {

    /**
     * Returns the table definition to be used in schema syncing
     *
     *
     * @return MysqlTableDefinition
     */
    public function get_table(): MysqlTableDefinition;

}
