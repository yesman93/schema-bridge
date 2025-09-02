<?php

namespace Lumio\DTO\Database;

use Traversable;

class MysqlIndexes implements \IteratorAggregate {

    /**
     * Indexes
     *
     * @author TB
     * @date 25.5.2025
     *
     * @var MysqlIndexDefinition[]
     */
    private array $_indexes = [];

    /**
     * Adds an index to the collection
     *
     * @author TB
     * @date 25.5.2025
     *
     * @param MysqlIndexDefinition $index
     *
     * @return void
     */
    public function add_index(MysqlIndexDefinition $index): void {
        $this->_indexes[] = $index;
    }

    /**
     * Get all indexes
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return MysqlIndexDefinition[]
     */
    public function get_indexes(): array {
        return $this->_indexes;
    }

    /**
     * Get an iterator for the indexes
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->_indexes);
    }

    /**
     * Converts the indexes to SQL query
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return string
     */
    public function to_sql(): string {

        $sql = [];

        foreach ($this->_indexes as $index) {
            $sql[] = $index->to_sql();
        }

        return implode(",\n", $sql);
    }

}
