<?php

namespace Lumio\Database;

class TypeValidator {

    /**
     * Validate given value against given database data type
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param mixed  $value  Value to validate
     * @param string $type Expected type ('int', 'varchar', 'text', 'date', 'datetime')
     *
     * @return bool
     */
    public static function validate($value, string $type): bool {

        $php_type = gettype($value);
        $type = strtolower($type);

        switch ($type) {

            case 'int':
                if ($php_type === 'string') {
                    return (string)(int)$value === (string)$value;
                }
                return $php_type === 'integer';

            case 'varchar':

                case 'text':
                return in_array($php_type, ['string', 'integer', 'double'], true);

            case 'date':
                return $php_type === 'string' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1;

            case 'datetime':
                return $php_type === 'string' &&
                    (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $value) === 1);

            default:
                return false;
        }
    }

}
