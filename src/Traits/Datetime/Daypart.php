<?php

namespace Lumio\Traits\Datetime;

class Daypart {

    /**
     * Returns the current daypart label or greeting
     *
     *
     * @param bool $greeting
     *
     * @return string
     */
    public static function get_current_daypart(bool $greeting = false): string {
        return self::get_daypart_by_time(date('H:i'), $greeting);
    }

    /**
     * Returns the daypart label or greeting for given time
     *
     *
     * @param string $time
     * @param bool $greeting
     *
     * @return string
     */
    public static function get_daypart_by_time(string $time, bool $greeting = false): string {

        $len = strlen($time);
        if ($len < 5 || $len > 8) {
            return '';
        }

        if ($time >= '02:00' && $time < '09:00') {
            return $greeting ? __tx('Good morning') : __tx('morning');
        }

        if ($time >= '09:00' && $time < '12:00') {
            return $greeting ? __tx('Good mid-morning') : __tx('mid-morning');
        }

        if ($time >= '12:00' && $time < '17:00') {
            return $greeting ? __tx('Good afternoon') : __tx('afternoon');
        }

        if (
            ($time >= '17:00' && $time <= '23:59') ||
            ($time >= '00:00' && $time < '02:00')
        ) {
            return $greeting ? __tx('Good evening') : __tx('evening');
        }

        return '';
    }

}
