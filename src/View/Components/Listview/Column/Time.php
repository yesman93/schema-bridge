<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Time extends Column {

    /**
     * Get time from the given row
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

        return date('G:i', strtotime($value));
    }

}
