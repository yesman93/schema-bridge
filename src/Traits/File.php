<?php

namespace Lumio\Traits;

trait File {

    public static function readable_size($bytes) : string {

        if (empty($bytes)) {
            return '0 B';
        }

        $gb = $bytes / 1024 / 1024 / 1024;
        $mb = $bytes / 1024 / 1024;
        $kb = $bytes / 1024;

        if ($gb > 1) {
            return number_format($gb, 2, ',', ' ') . ' GB';
        } elseif ($mb > 1) {
            return number_format($mb, 2, ',', ' ') . ' MB';
        } elseif ($kb > 1) {
            return number_format($kb, 2, ',', ' ') . ' kB';
        } else {
            return number_format($bytes, 2, ',', ' ') . ' B';
        }
    }

}
