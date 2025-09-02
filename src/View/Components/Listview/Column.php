<?php

namespace Lumio\View\Components\Listview;

abstract class Column {

    /**
     * Column name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    protected string $_name;

    /**
     * Column label
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    protected ?string $_label;

    /**
     * Column width
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var int|null
     */
    protected ?int $_width = null;

    /**
     * Column class
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    protected ?string $_class = null;

    /**
     * Whether the column is sortable
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var bool
     */
    protected bool $_sortable = false;

    /**
     * Sort column
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string|null
     */
    protected ?string $_sort_column = null;

    /**
     * Listview Column
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, ?string $label = null, ?int $width = null, ?string $class = null) {

        $this->_name = $name;
        $this->_label = $label;
        $this->_width = $width;
        $this->_class = $class;
    }

    /**
     * Get column name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get column label
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return string|null
     */
    public function get_label(): ?string {
        return $this->_label;
    }

    /**
     * Get column width
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return int|null
     */
    public function get_width(): ?int {
        return $this->_width;
    }

    /**
     * Get column class
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return string|null
     */
    public function get_class(): ?string {
        return $this->_class;
    }

    /**
     * Get HTML of the value from given row
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        $value = $row[$this->_name] ?? null;
        $value = (string)$value;

        return htmlspecialchars($value);
    }

    /**
     * Render HTML of the value from given row
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return void
     */
    public function render(mixed $row): void {
        echo $this->get($row);
    }

    /**
     * Set the column as sortable
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string|null $column
     *
     * @return self
     */
    public function sortable(?string $column = null): self {

        $this->_sortable = true;
        $this->_sort_column = $column;

        return $this;
    }

    /**
     * Get whether the column is sortable
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return bool
     */
    public function is_sortable(): bool {
        return $this->_sortable;
    }

    /**
     * Get the column by which to sort
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return string|null
     */
    public function get_sort_column(): ?string {
        return $this->_sort_column;
    }

}
