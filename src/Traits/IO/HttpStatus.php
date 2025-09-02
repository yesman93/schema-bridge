<?php

namespace Lumio\Traits\IO;

trait HttpStatus {

    /**
     * HTTP status code - 200 OK
     *
     * @var int
     */
    const HTTP_200 = 200;

    /**
     * HTTP status code - 201 Created
     *
     * @var int
     */
    const HTTP_201 = 201;

    /**
     * HTTP status code - 204 No Content
     *
     * @var int
     */
    const HTTP_204 = 204;

    /**
     * HTTP status code - 301 Moved Permanently
     *
     * @var int
     */
    const HTTP_301 = 301;

    /**
     * HTTP status code - 302 Found
     *
     * @var int
     */
    const HTTP_302 = 302;

    /**
     * HTTP status code - 400 Bad Request
     *
     * @var int
     */
    const HTTP_400 = 400;

    /**
     * HTTP status code - 401 Unauthorized
     *
     * @var int
     */
    const HTTP_401 = 401;

    /**
     * HTTP status code - 403 Forbidden
     *
     * @var int
     */
    const HTTP_403 = 403;

    /**
     * HTTP status code - 404 Not Found
     *
     * @var int
     */
    const HTTP_404 = 404;

    /**
     * HTTP status code - 405 Method Not Allowed
     *
     * @var int
     */
    const HTTP_405 = 405;

    /**
     * HTTP status code - 500 Internal Server Error
     *
     * @var int
     */
    const HTTP_500 = 500;

    /**
     * HTTP status code - 503 Service Unavailable
     *
     * @var int
     */
    const HTTP_503 = 503;

    /**
     * Default HTTP status bodies
     *
     * @var array
     */
    private static array $_DEFAULT_BODIES = [
        self::HTTP_200 => 'OK',
        self::HTTP_201 => 'Created',
        self::HTTP_204 => 'No Content',
        self::HTTP_301 => 'Moved Permanently',
        self::HTTP_302 => 'Found',
        self::HTTP_400 => 'Bad Request',
        self::HTTP_401 => 'Unauthorized',
        self::HTTP_403 => 'Forbidden',
        self::HTTP_404 => 'Not Found',
        self::HTTP_405 => 'Method Not Allowed',
        self::HTTP_500 => 'Internal Server Error',
        self::HTTP_503 => 'Service Unavailable',
    ];

    /**
     * Get default body for given status code
     *
     * @param int $status_code
     *
     * @return string
     */
    public static function get_body(int $status_code): string {
        return self::$_DEFAULT_BODIES[$status_code] ?? 'HTTP ' . $status_code;
    }

}
