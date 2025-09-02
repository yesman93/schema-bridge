<?php

namespace Lumio\Traits\Log;

trait LogLevel {

    /**
     * Log level - emergency
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_EMERGENCY = 'emergency';

    /**
     * Log level - alert
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_ALERT = 'alert';

    /**
     * Log level - critical
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_CRITICAL = 'critical';

    /**
     * Log level - error
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_ERROR = 'error';

    /**
     * Log level - warning
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_WARNING = 'warning';

    /**
     * Log level - notice
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_NOTICE = 'notice';

    /**
     * Log level - info
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_INFO = 'info';

    /**
     * Log level - debug
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private const _LEVEL_DEBUG = 'debug';

    /**
     * Get human-readable description of given log level
     *
     * @author TB
     * @date 9.5.2025
     *
     * @param string $level
     *
     * @return string
     */
    public static function get_level_description(string $level): string {

        return match (strtolower($level)) {
            self::_LEVEL_EMERGENCY => __tx('Emergency'),
            self::_LEVEL_ALERT => __tx('Alert'),
            self::_LEVEL_CRITICAL => __tx('Critical'),
            self::_LEVEL_ERROR => __tx('Error'),
            self::_LEVEL_WARNING => __tx('Warning'),
            self::_LEVEL_NOTICE => __tx('Notice'),
            self::_LEVEL_INFO => __tx('Info'),
            self::_LEVEL_DEBUG  => __tx('Debug'),
            default => __tx('Unknown'),
        };
    }

    /**
     * Get all supported log levels
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return array
     */
    protected static function _levels() : array {

        return [
            self::_LEVEL_EMERGENCY,
            self::_LEVEL_ALERT,
            self::_LEVEL_CRITICAL,
            self::_LEVEL_ERROR,
            self::_LEVEL_WARNING,
            self::_LEVEL_NOTICE,
            self::_LEVEL_INFO,
            self::_LEVEL_DEBUG
        ];
    }

    /**
     * Get all supported log levels as options for select input
     *
     * @author TB
     * @date 11.5.2025
     *
     * @param bool $first_empty
     *
     * @return array
     */
    public static function get_level_options(bool $first_empty = false) : array {

        $ret = [];

        if ($first_empty) {
            $ret[] = ['value' => '', 'label' => __tx('- not selected -')];
        }

        foreach (self::_levels() as $level) {

            $ret[] = [
                'value' => $level,
                'label' => self::get_level_description($level)
            ];
        }

        return $ret;
    }

    /**
     * Get color class for given log level
     *
     * @author TB
     * @date 11.5.2025
     *
     * @param string $level
     *
     * @return string
     */
    public static function get_level_color(string $level): string {

        return match (strtolower($level)) {
            self::_LEVEL_EMERGENCY => 'danger',
            self::_LEVEL_ALERT     => 'danger',
            self::_LEVEL_CRITICAL  => 'danger',
            self::_LEVEL_ERROR     => 'danger',
            self::_LEVEL_WARNING   => 'warning',
            self::_LEVEL_NOTICE    => 'primary',
            self::_LEVEL_INFO      => 'info',
            self::_LEVEL_DEBUG     => 'secondary',
            default                => 'dark',
        };
    }

}
