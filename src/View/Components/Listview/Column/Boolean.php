<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Boolean extends Column {

    /**
     * Get HTML code of the column
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        if (!empty($row[$this->_name] ?? false)) {
            $label = __tx('Yes');
            $bg_class = 'text-bg-success';
        } else {
            $label = __tx('No');
            $bg_class = 'text-bg-danger';
        }

        return '<span class="badge ' . $bg_class . '">' . $label . '</span>';
    }

}
