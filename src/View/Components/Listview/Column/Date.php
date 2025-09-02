<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Date extends Column {

    /**
     * Get the date in the format j.n.Y
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
        if (!strtotime($value)) {
            return '';
        }

        return date('j.n.Y', strtotime($value));
    }

}
