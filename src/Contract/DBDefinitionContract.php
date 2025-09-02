<?php

namespace Lumio\Contract;

use Lumio\DTO\Database\MysqlTableDefinition;

/**
 * Contract for any model or class that can define a database table schema
 *
 * @author TB
 * @date 23.5.2025
 */
interface DBDefinitionContract {

    /**
     * Returns the table definition to be used in schema syncing
     *
     * @author TB
     * @date 23.5.2025
     *
     * @return MysqlTableDefinition
     */
    public function get_table(): MysqlTableDefinition;

}
