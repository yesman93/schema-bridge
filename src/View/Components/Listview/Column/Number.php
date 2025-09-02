<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Number extends Column {

    /**
     * Number of decimals to display
     *
     *
     * @var int|null
     */
    protected ?int $_decimals;

    /**
     * Number column
     *
     *
     * @param string $name
     * @param string $label
     * @param int|null $width
     * @param int|null $decimals
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, string $label, ?int $width = null, ?int $decimals = null, ?string $class = null) {
        parent::__construct($name, $label, $width, $class);
        $this->_decimals = $decimals;
    }

    /**
     * Get HTML of the column
     *
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        $value = $row[$this->_name] ?? null;
        if (!is_numeric($value)) {
            return '';
        }

        return $this->_decimals !== null ? number_format((float)$value, $this->_decimals) : (string)$value;
    }
}
