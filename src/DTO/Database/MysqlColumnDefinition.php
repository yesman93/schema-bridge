<?php

namespace Lumio\DTO\Database;

use JsonSerializable;
use Lumio\Traits;

class MysqlColumnDefinition implements JsonSerializable {

    use Traits\Database\DataType;
    use Traits\Database\Collation;
    use Traits\Database\Attribute;
    use Traits\Database\Index;

    /**
     * MySQL table definition
     *
     *
     * @param string        $name
     * @param string        $type
     * @param int|null      $length
     * @param string|null   $collation
     * @param string|null   $attribute
     * @param bool          $nullable
     * @param mixed|null    $default
     * @param bool          $auto_increment
     * @param string|null   $comment
     * @param string|null   $index
     *
     * @return MysqlColumnDefinition
     */
    public function __construct(
        private string $name,
        private string $type,
        private ?int $length = null,
        private ?string $collation = null,
        private ?string $attribute = null,
        private bool $nullable = false,
        private mixed $default = null,
        private bool $auto_increment = false,
        private ?string $comment = null,
        private ?string $index = null,
    ) {

        if (in_array($this->type, [self::TYPE_VARCHAR, self::TYPE_TEXT, self::TYPE_LONGTEXT])) {
            $this->collation = $this->collation ?? self::COLLATION_UTF8MB4_CZECH_CI;
        }
    }

    /**
     * Returns SQL query for creating the column
     *
     *
     * @return string
     */
    public function to_sql(): string {

        $parts = [];

        $parts[] = '`' . $this->name . '`';

        $type = $this->type;
        if ($this->length !== null) {
            $type .= '(' . $this->length . ')';
        }
        $parts[] = $type;

        if ($this->attribute !== null) {
            $parts[] = $this->attribute;
        }

        if ($this->collation !== null) {
            $parts[] = 'COLLATE ' . $this->collation;
        }

        $parts[] = $this->nullable ? 'NULL' : 'NOT NULL';

        if ($this->default !== null) {
            $default = strtoupper($this->default) === 'CURRENT_TIMESTAMP' ? $this->default : '\'' . $this->default . '\'';
            $parts[] = 'DEFAULT ' . $default;
        }

        if ($this->auto_increment) {
            $parts[] = 'AUTO_INCREMENT';
        }

        if ($this->comment !== null && $this->comment !== '') {
            $parts[] = 'COMMENT \'' . $this->comment . '\'';
        }

        return implode(' ', $parts);
    }

    /**
     * Returns column name
     *
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Returns column type
     *
     *
     * @return string
     */
    public function get_type(): string {
        return $this->type;
    }

    /**
     * Returns column length
     *
     *
     * @return int|null
     */
    public function get_length(): ?int {
        return $this->length;
    }

    /**
     * Returns column collation
     *
     *
     * @return string|null
     */
    public function get_collation(): ?string {
        return $this->collation;
    }

    /**
     * Returns column attribute
     *
     *
     * @return string|null
     */
    public function get_attribute(): ?string {
        return $this->attribute;
    }

    /**
     * Returns if column is nullable
     *
     *
     * @return bool
     */
    public function is_nullable(): bool {
        return $this->nullable;
    }

    /**
     * Returns column default value
     *
     *
     * @return mixed|null
     */
    public function get_default(): mixed {
        return $this->default;
    }

    /**
     * Returns if column is auto increment
     *
     *
     * @return bool
     */
    public function is_auto_increment(): bool {
        return $this->auto_increment;
    }

    /**
     * Returns column comment
     *
     *
     * @return string|null
     */
    public function get_comment(): ?string {
        return $this->comment;
    }

    /**
     * Returns column index
     *
     *
     * @return string|null
     */
    public function get_index(): ?string {
        return $this->index;
    }

    /**
     * Returns JSON representation of the column definition
     *
     *
     * @return array
     */
    public function jsonSerialize(): array {

        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'collation' => $this->collation,
            'attribute' => $this->attribute,
            'nullable' => $this->nullable,
            'default' => $this->default,
            'auto_increment' => $this->auto_increment,
            'comment' => $this->comment,
            'index' => $this->index
        ];
    }
}


