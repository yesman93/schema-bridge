<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Badge extends Column {

    /**
     * Background class for the badge
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    protected string $_bg_class;

    /**
     * Badge column
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $name
     * @param string $label
     * @param int|null $width
     * @param string $bg_class
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, string $label, ?int $width = null, string $bg_class = 'bg-secondary', ?string $class = null) {
        parent::__construct($name, $label, $width, $class);
        $this->_bg_class = $bg_class;
    }

    /**
     * Get HTML of the badge
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        $value = $row[$this->_name] ?? '';
        $value = (string)$value;
        $value = htmlspecialchars($value);

        return '<span class="badge ' . $this->_bg_class . '">' . $value . '</span>';
    }
}
