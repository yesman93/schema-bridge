<?php

namespace Lumio\IO;

class RequestSanitizer {

    /**
     * Sanitize a string value
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function sanitize_string(mixed $value): string {

        if (!is_string($value)) {
            return '';
        }

        return trim(strip_tags($value));
    }

    /**
     * Sanitize an integer value
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function sanitize_int(mixed $value): int {
        return filter_var($value, FILTER_VALIDATE_INT) ? : 0;
    }

    /**
     * Sanitize an array recursively
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function sanitize_array(mixed $value): array {

        if (!is_array($value)) {
            return [];
        }

        return array_map([self::class, 'sanitize_value'], $value);
    }

    /**
     * General sanitize function based on type
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function sanitize_value(mixed $value): mixed {

        if (is_array($value)) {
            return self::sanitize_array($value);
        }

        if (is_int($value)) {
            return self::sanitize_int($value);
        }

        return self::sanitize_string($value);
    }

    /**
     * Sanitize values in the $_SERVER array
     *
     * @param array $array
     *
     * @return array
     */
    public static function sanitize_server(array $array) : array {

        foreach ($array as $key => $value) {
            $array[$key] = filter_input(INPUT_SERVER, $key);
        }

        return $array;
    }

}
