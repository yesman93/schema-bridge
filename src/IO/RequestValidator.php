<?php

namespace Lumio\IO;

use Lumio\DTO\Database\MysqlColumnDefinition;
use Lumio\IO\Request;
use Lumio\IO\MessageBag;
use Lumio\Model\BaseModel;
use Lumio\Traits;
use Lumio\Utilities\Is;

class RequestValidator {

    use Traits\Database\DataType;

    /**
     * Indicator if the request is valid
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    protected static bool $is_valid = true;

    /**
     * Validate given request based on given model
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param Request $request
     * @param BaseModel  $model
     *
     * @return void
     */
    public static function validate(Request $request, BaseModel $model): void {

        self::$is_valid = true;

        $data = [];
        $data = array_merge($data, $request->get());
        $data = array_merge($data, $request->post());

        $columns = $model->get_columns();

        foreach ($data as $name => $value) {

            $column = $columns->get($name);
            if (empty($column) || !$column instanceof MysqlColumnDefinition) {
                continue;
            }

            if (!$column->is_nullable() && empty($value)) {
                self::$is_valid = false;
                MessageBag::error(__tx('Item cannot be empty!'), $name);
            } else {

                $type = strtoupper($column->get_type());
                if (!self::validate_type($value, $type)) {
                    self::$is_valid = false;
                    MessageBag::error(self::_get_type_error_message($type), $name);
                } else {

                    $semantic_error = self::_validate_semantic($value, $name);
                    if (!empty($semantic_error)) {
                        self::$is_valid = false;
                        MessageBag::error($semantic_error, $name);
                    }
                }
            }
        }
    }

    /**
     * Get if the request is valid
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public static function is_valid(): bool {
        return self::$is_valid;
    }

    /**
     * Validate given value based on given type
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param mixed $value
     * @param string $type
     *
     * @return bool
     */
    protected static function validate_type(mixed $value, string $type): bool {

        return match ($type) {

            self::TYPE_INT,
            self::TYPE_TINYINT,
            self::TYPE_SMALLINT,
            self::TYPE_MEDIUMINT,
            self::TYPE_BIGINT => filter_var($value, FILTER_VALIDATE_INT) !== false,

            self::TYPE_DECIMAL,
            self::TYPE_FLOAT,
            self::TYPE_DOUBLE => filter_var($value, FILTER_VALIDATE_FLOAT) !== false,

            self::TYPE_CHAR,
            self::TYPE_VARCHAR,
            self::TYPE_TEXT,
            self::TYPE_TINYTEXT,
            self::TYPE_MEDIUMTEXT,
            self::TYPE_LONGTEXT => is_scalar($value),

            self::TYPE_DATE => (bool)preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $value),
            self::TYPE_DATETIME,
            self::TYPE_TIMESTAMP => (bool)preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}(\:\d{2})?$/', $value),
            self::TYPE_TIME => (bool)preg_match('/^\d{2,3}\:\d{2}(\:\d{2})?$/', $value),
            self::TYPE_YEAR => preg_match('/^\d{4}$/', $value),

            default => true // unknown = pass
        };
    }

    /**
     * Get given type specific error message
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $type
     * @return string
     */
    private static function _get_type_error_message(string $type): string {

        return match ($type) {

            self::TYPE_INT,
            self::TYPE_TINYINT,
            self::TYPE_SMALLINT,
            self::TYPE_MEDIUMINT,
            self::TYPE_BIGINT => __tx('Value must be an integer!'),

            self::TYPE_DECIMAL,
            self::TYPE_FLOAT,
            self::TYPE_DOUBLE => __tx('Value must be a number!'),

            self::TYPE_CHAR,
            self::TYPE_VARCHAR,
            self::TYPE_TEXT,
            self::TYPE_TINYTEXT,
            self::TYPE_MEDIUMTEXT,
            self::TYPE_LONGTEXT => __tx('Value must be text!'),

            self::TYPE_DATE => __tx('Value must be in date format!'),

            self::TYPE_DATETIME,
            self::TYPE_TIMESTAMP => __tx('Value must be in datetime format!'),

            self::TYPE_TIME => __tx('Value must be in time format!'),

            self::TYPE_YEAR => __tx('Value must be a four digit number!'),

            default => __tx('Invalid value!')
        };
    }

    /**
     * Validate given value based on given name
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param mixed $value
     * @param string $name
     *
     * @return string|null
     */
    private static function _validate_semantic(mixed $value, string $name): ?string {

        if (str_contains($name, 'email') && !Is::email($value)) {
            return __tx('Value must be a valid email address!');
        }

        return null;
    }

}
