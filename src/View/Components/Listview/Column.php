<?php

namespace Lumio\View\Components\Listview;

abstract class Column {

    /**
     * Column name
     *
     * @var string
     */
    protected string $_name;

    /**
     * Column label
     *
     * @var string|null
     */
    protected ?string $_label;

    /**
     * Column width
     *
     * @var int|null
     */
    protected ?int $_width = null;

    /**
     * Column class
     *
     * @var string|null
     */
    protected ?string $_class = null;

    /**
     * Whether the column is sortable
     *
     * @var bool
     */
    protected bool $_sortable = false;

    /**
     * Sort column
     *
     * @var string|null
     */
    protected ?string $_sort_column = null;

    /**
     * Listview Column
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
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get column label
     *
     * @return string|null
     */
    public function get_label(): ?string {
        return $this->_label;
    }

    /**
     * Get column width
     *
     * @return int|null
     */
    public function get_width(): ?int {
        return $this->_width;
    }

    /**
     * Get column class
     *
     * @return string|null
     */
    public function get_class(): ?string {
        return $this->_class;
    }

    /**
     * Get HTML of the value from given row
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
     * @return bool
     */
    public function is_sortable(): bool {
        return $this->_sortable;
    }

    /**
     * Get the column by which to sort
     *
     * @return string|null
     */
    public function get_sort_column(): ?string {
        return $this->_sort_column;
    }

}
