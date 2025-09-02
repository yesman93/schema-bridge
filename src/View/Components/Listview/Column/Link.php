<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components;
use Lumio\View\Components\Listview\Column;

class Link extends Column {

    /**
     * Key for retrieval of the label
     *
     *
     * @var string|null
     */
    private ?string $_name_label = null;

    /**
     * Link column
     *
     *
     * @param string $name
     * @param string|null $name_label
     * @param int|null $width
     * @param int|null $width
     *
     * @return void
     */
    public function __construct(string $name, ?string $name_label = null, string $label = '', ?int $width = null) {
        parent::__construct($name, $label, $width);
        $this->_name_label = $name_label;
    }

    /**
     * Get HTML code of the link
     *
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        $value = $row[$this->_name] ?? '';
        if (!is_scalar($value)) {
            return '';
        }

        $label = $row[$this->_name_label] ?? '';
        if (empty($label)) {
            $label = $value;
        }

        return Components\Link::a(
            href: $value,
            label: $label,
        )->get();
    }

}
