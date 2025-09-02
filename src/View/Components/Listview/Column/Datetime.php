<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Datetime extends Column {

    /**
     * Get date and time from the given row
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

        return date('j.n.Y G:i', strtotime($value));
    }

}
