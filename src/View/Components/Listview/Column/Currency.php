<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Currency extends Column {

    /**
     * Currency symbol
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    protected string $_currency;

    /**
     * Currency column
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $name
     * @param string $label
     * @param int|null $width
     * @param string $currency
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, string $label, ?int $width = null, string $currency, ?string $class = null) {
        parent::__construct($name, $label, $width, $class);
        $this->_currency = $currency;
    }

    /**
     * Get HTML of the currency column
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
        if (!is_numeric($value)) {
            return '';
        }

        return number_format((float)$value, 2) . ' ' . htmlspecialchars($this->_currency);
    }
}

