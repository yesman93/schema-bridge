<?php

namespace Lumio\DTO\Database;

use JsonSerializable;
use Lumio\Traits;

class MysqlIndexDefinition implements JsonSerializable {

    use Traits\Database\Index;

    /**
     * Index name
     *
     * @author TB
     * @date 25.5.2025
     *
     * @var string
     */
    private string $_name;

    /**
     * Index type (e.g., PRIMARY, UNIQUE, INDEX)
     *
     * @author TB
     * @date 25.5.2025
     *
     * @var string
     */
    private string $_type;

    /**
     * Columns included in the index
     *
     * @author TB
     * @date 25.5.2025
     *
     * @var array
     */
    private array $_columns;

    /**
     * Constructor for MysqlIndexDefinition
     *
     * @author TB
     * @date 25.5.2025
     *
     * @param string $type
     * @param array $columns
     * @param string|null $name
     *
     * @return void
     */
    public function __construct(string $type, array $columns, ?string $name = null) {
        $this->_type = $type;
        $this->_columns = $columns;
        $this->_name = $name ?? '';
    }

    /**
     * Get name of the index
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get type of the index
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return string
     */
    public function get_type(): string {
        return $this->_type;
    }

    /**
     * Get columns included in the index
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return array
     */
    public function get_columns(): array {
        return $this->_columns;
    }

    /**
     * Convert the index definition to SQL query
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return string
     */
    public function to_sql(): string {

        $columns = '`' . implode('`, `', $this->_columns) . '`';

        $type = strtoupper($this->_type);

        if ($this->_name === '') {
            $name = '`idx_' . implode('_', $this->_columns) . '`';
        } else {
            $name = '`' . $this->_name . '`';
        }

        return match ($type) {
            self::INDEX_PRIMARY => "PRIMARY KEY ($columns)",
            self::INDEX_UNIQUE  => "UNIQUE KEY $name ($columns)",
            self::INDEX_INDEX => "KEY $name ($columns)",
            default => "KEY $name ($columns)",
        };
    }

    /**
     * Get a unique key for comparison purposes
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return string
     */
    public function get_comparison_key(): string {

        if ($this->_name === '' && strtolower($this->_type) === strtolower(self::INDEX_PRIMARY)) {
            $this->_name = 'primary';
        }

        if ($this->_name === '') {
            $this->_name = 'idx_' . implode('_', $this->_columns);
        }

        return $this->_type . '-' . $this->_name . '-' . implode('-', $this->_columns);
    }

    /**
     * Returns JSON representation of the index definition
     *
     * @author TB
     * @date 25.5.2025
     *
     * @return array
     */
    public function jsonSerialize(): array {

        return [
            'name' => $this->_name,
            'type' => $this->_type,
            'columns' => $this->_columns,
        ];
    }
}

