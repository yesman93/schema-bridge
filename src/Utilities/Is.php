<?php

namespace Lumio\Utilities;

use Lumio\Localization\Language;
use Lumio\Config;

class Is {

    /**
     * Check if the input is a valid date
     *
     *
     * @param string $date
     *
     * @return bool
     */
    public static function date(string $date): bool {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }

    /**
     * Check if the input is a valid datetime
     *
     *
     * @param string $val
     *
     * @return bool
     */
    public static function datetime(string $val): bool {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{1,2}:\d{1,2}(:\d{1,2})?$/', $val);
    }

    /**
     * Check if the input is a valid DB time
     *
     *
     * @param string $input
     *
     * @return bool
     */
    public static function db_time(string $input): bool {

        return (bool) (
            preg_match('/^\d{2,3}:\d{2}:\d{2}$/', $input) ||
            preg_match('/^\d{2,3}:\d{2}$/', $input)
        );
    }

    /**
     * Check if the date is empty
     *
     *
     * @param mixed $input
     *
     * @return bool
     */
    public static function empty_date(mixed $input): bool {
        return empty($input) || $input === '0000-00-00' || $input === '1970-01-01';
    }

    /**
     * Check if the time is empty
     *
     *
     * @param mixed $input
     *
     * @return bool
     */
    public static function empty_time(mixed $input): bool {
        return empty($input) || in_array($input, ['00:00', '00:00:00', '000:00:00'], true);
    }

    /**
     * Check if the datetime is empty
     *
     *
     * @param mixed $input
     *
     * @return bool
     */
    public static function empty_datetime(mixed $input): bool {

        return empty($input) || in_array(
            $input,
            [
                '0000-00-00 00:00',
                '0000-00-00 00:00:00',
                '0000-00-00 000:00:00',
                '1970-01-01 00:00',
                '1970-01-01 00:00:00',
                '1970-01-01 000:00:00',
            ],
            true
        );
    }

    /**
     * Check if the input is an integer
     *
     *
     * @param mixed $input
     *
     * @return bool
     */
    public static function is_int(mixed $input): bool {
        return (bool) preg_match('/^\d+$/', (string) $input);
    }

    /**
     * Check if the input is a float
     *
     *
     * @param mixed $input
     *
     * @return bool
     */
    public static function is_float(mixed $input): bool {

        $input = str_replace(',', '.', (string) $input);

        return is_numeric($input) && (float) $input == $input;
    }

    /**
     * Check if the input is a valid email address
     *
     *
     * @param string $email
     *
     * @return bool
     */
    public static function email(string $email): bool {

        if (empty($email)) {
            return false;
        }

        return (bool) preg_match(
            '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-z0-9-]+)*(\.[a-zA-Z]{2,63})$/',
            $email
        );
    }

    /**
     * Check if the input is a valid phone number
     *
     *
     * @param string $phone
     *
     * @return bool
     */
    public static function phone(string $phone): bool {

        if (empty($phone)) {
            return false;
        }

        $phone = preg_replace('/\s+/', '', trim($phone));
        return (bool) preg_match('/^\+?\d{9,12}$/', $phone);
    }

    /**
     * Check if the input is a valid postal code
     *
     *
     * @param string $code
     * @param string|null $country_iso
     *
     * @return bool
     */
    public static function postal_code(string $code, ?string $country_iso = null): bool {

        if (empty($country_iso)) {
            $country_iso = Language::CZ;
        }

        try {
            $config = Config::get('zip_codes');
            $regex = $config[strtoupper($country_iso)] ?? null;
        } catch (\Exception $e) {
            $regex = null;
        }

        if (empty($regex)) {
            return true;
        }

        return (bool) preg_match('/^' . $regex . '$/', $code);
    }

}
