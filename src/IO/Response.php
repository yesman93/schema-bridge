<?php

namespace Lumio\IO;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Lumio\Log\Logger;
use Lumio\Traits\IO\HttpStatus;

class Response {

    use HttpStatus;

    /**
     * HTTP status code
     *
     * @var int
     */
    private int $_status_code = self::HTTP_200;

    /**
     * HTTP body
     *
     * @var string
     */
    private string $_body = '';

    /**
     * HTTP headers
     *
     * @var array
     */
    private array $_headers = [];

    /**
     * set HTTP status code
     *
     * @param int $status_code
     *
     * @return \Lumio\IO\Response
     */
    public function status(int $status_code): self {
        $this->_status_code = $status_code;
        return $this;
    }

    /**
     * set HTTP body
     *
     * @param string $body
     *
     * @return \Lumio\IO\Response
     */
    public function body(string $body): self {
        $this->_body = $body;
        return $this;
    }

    /**
     * set HTTP header
     *
     * @param string|array $header The header to set - it accepts ready string like `"Location: /path"` or array - associative like this: `['location' => '/path']`, or non-associative like this: `['location', '/path']`. If the right format is not met, the header will not be set
     *
     * @return \Lumio\IO\Response
     */
    public function header(string|array $header): self {

        if (is_string($header)) {

            if (strpos($header, ':') === false) {
                return $this;
            }

            [$name, $value] = explode(':', $header, 2);

        } else {

            if (!array_is_assoc($header)) {

                if (count($header) <= 1) {
                    return $this;
                }

                [$name, $value] = $header;

            } else {
                $name = array_key_first($header);
                $value = reset($header);
            }
        }

        $this->_headers[$name] = $value;

        return $this;
    }

    /**
     * Check if the response has given header
     *
     * @param string $name
     *
     * @return bool
     */
    public function has_header(string $name): bool {

        $name = strtolower($name);
        foreach ($this->_headers as $header_name => $header_value) {

            if (strtolower($header_name) === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the response has given header with given value
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function header_equals(string $name, string $value): bool {

        $name = strtolower($name);
        $value = strtolower($value);
        foreach ($this->_headers as $header_name => $header_value) {

            if (strtolower($header_name) === $name && strtolower($header_value) === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the response has given header containing given value (case-insensitive)
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function header_contains(string $name, string $value): bool {

        $name = strtolower($name);
        $value = strtolower($value);
        foreach ($this->_headers as $header_name => $header_value) {

            if (strtolower($header_name) === $name && strpos(strtolower($header_value), $value) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the response is a download response
     *
     * @return bool
     */
    public function is_download(): bool {

        return $this->header_equals('Content-Description', 'File Transfer')
            && $this->header_contains('Content-Disposition', 'attachment');
    }

    /**
     * clear the response - resets status code, body and headers
     *
     * @return \Lumio\IO\Response
     */
    public function clear(): self {
        $this->_status_code = self::HTTP_200;
        $this->_body = '';
        $this->_headers = [];
        return $this;
    }

    /**
     * send the response
     *
     * @return void
     *
     * @throws Exception
     */
    public function send(): void {

        http_response_code($this->_status_code);

        foreach ($this->_headers as $name => $value) {

            $header = $name;
            $header .= ': ';
            $header .= $value;

            header($header);
        }

        echo $this->_body;
        exit;
    }

    /**
     * Send response headers only
     *
     * @return void
     */
    public function send_headers(): void {

        http_response_code($this->_status_code);

        foreach ($this->_headers as $name => $value) {

            $header = $name;
            $header .= ': ';
            $header .= $value;

            header($header);
        }
    }

    /**
     * send JSON response
     *
     * @param array $data
     *
     * @return void
     */
    public function json(array $data) : void {

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }

    /**
     * Show failure page
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function fail(?\Throwable $e = null) : void {

        lumio_fail($this->_body, $this->_status_code, $e);

        exit;
    }

    /**
     * Redirect to the given destination
     *
     * @param string $destination
     * @param int $status_code
     *
     * @return void
     */
    #[NoReturn] public function redirect(string $destination, int $status_code = self::HTTP_302): void {

        $normalized = normalize_url($destination);

        if (ob_get_length()) {
            ob_clean();
        }

        http_response_code($status_code);

        header('Location: ' . $normalized);

        exit;
    }

}
