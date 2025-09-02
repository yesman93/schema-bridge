<?php

namespace Lumio\DTO\Database;

use Lumio\Traits;

class MysqlTableDefinition {

    use Traits\Database\Engine;
    use Traits\Database\Collation;

    /**
     * MySQL table name
     *
     *
     * @var string
     */
    private string $_name;

    /**
     * MySQL table columns
     *
     *
     * @var MysqlColumns
     */
    private MysqlColumns $_columns;

    /**
     * MySQL table indexes
     *
     *
     * @var MysqlIndexes
     */
    private MysqlIndexes $_indexes;

    /**
     * MySQL table engine
     *
     *
     * @var string
     */
    private string $_engine;

    /**
     * MySQL table collation
     *
     *
     * @var string
     */
    private string $_collation;

    /**
     * MySQL table comment
     *
     *
     * @var string|null
     */
    private ?string $_comment = null;

    /**
     * MySQL table
     *
     *
     * @param string            $name
     * @param MysqlColumns      $columns
     * @param MysqlIndexes|null $indexes
     * @param string            $engine
     * @param string            $collation
     * @param string|null       $comment
     *
     * @return void
     */
    public function __construct(
        string          $name,
        MysqlColumns    $columns,
        ?MysqlIndexes   $indexes = null,
        string          $engine = self::ENGINE_INNODB,
        string          $collation = self::COLLATION_UTF8MB4_CZECH_CI,
        ?string         $comment = null
    ) {

        $this->_name = $name;
        $this->_columns = $columns;
        $this->_indexes = $indexes ?? new MysqlIndexes();
        $this->_engine = $engine;
        $this->_collation = $collation;
        $this->_comment = $comment;
    }

    /**
     * Returns SQL query for creating the table
     *
     *
     * @return string
     */
    public function to_sql(): string {

        $columns_sql = $this->_columns->to_sql();

        $indexes_sql = $this->_indexes->to_sql();
        if (!empty($indexes_sql)) {
            $indexes_sql = ",\n" . $indexes_sql;
        }

        $comment_sql = $this->_comment ? ' COMMENT=\'' . $this->_comment . '\'' : '';

        $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->_name . '`';
        $sql .= " (\n" . $columns_sql . $indexes_sql . "\n)";
        $sql .= ' ENGINE=' . $this->_engine;
        $sql .= ' DEFAULT COLLATE=' . $this->_collation;
        $sql .= $comment_sql;

        return $sql;
    }

    /**
     * Get table name
     *
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get columns
     *
     *
     * @return MysqlColumns
     */
    public function get_columns(): MysqlColumns {
        return $this->_columns;
    }

    /**
     * Get indexes
     *
     *
     * @return MysqlIndexes
     */
    public function get_indexes(): MysqlIndexes {
        return $this->_indexes;
    }

    /**
     * Get engine
     *
     *
     * @return string
     */
    public function get_engine(): string {
        return $this->_engine;
    }

    /**
     * Get collation
     *
     *
     * @return string
     */
    public function get_collation(): string {
        return $this->_collation;
    }

    /**
     * Get comment
     *
     *
     * @return string|null
     */
    public function get_comment(): ?string {
        return $this->_comment;
    }

}



