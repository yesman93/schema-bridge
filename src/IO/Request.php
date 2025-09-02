<?php

namespace Lumio\IO;

use Lumio\DTO\File\UploadedFile;
use Lumio\DTO\Model\Sorting;
use Lumio\IO\RequestSanitizer;

class Request {

    /**
     * ID of the request
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    private string $_id = '';

    /**
     * data from $_GET
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private array $_get = [];

    /**
     * data from $_POST
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private array $_post = [];

    /**
     * data from $_REQUEST
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private array $_request = [];

    /**
     * data from $_FILES
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private array $_files = [];

    /**
     * data from $_SERVER
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private array $_server = [];

    /**
     * request URI
     *
     * @author TB
     * @date 29.4.2025
     *
     * @var string
     */
    private string $_request_uri = '';

    /**
     * Current page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var mixed
     */
    private mixed $_page = null;

    /**
     * Data for filtering
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var array
     */
    private array $_filter_data = [];

    /**
     * Sorting
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var Sorting|null
     */
    private ?Sorting $_sorting = null;

    /**
     * Headers
     *
     * @author TB
     * @date 17.5.2025
     *
     * @var array|null
     */
    private ?array $_headers = null;

    /**
     * Incoming JSON data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @var array|null
     */
    private ?array $_json = null;

    /**
     * Incoming XML data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @var array|null
     */
    private ?array $_xml = null;

    /**
     * request - get, post, files, server
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     */
    public function __construct() {

        $this->_get = RequestSanitizer::sanitize_array($_GET);
        $this->_post = RequestSanitizer::sanitize_array($_POST);
        $this->_request = RequestSanitizer::sanitize_array($_REQUEST);
        $this->_files = $_FILES;
        $this->_server = RequestSanitizer::sanitize_server($_SERVER);

        $this->_parse_uri();

        $this->_extract_filter_data($this->_get);
        $this->_extract_filter_data($this->_post);
        $this->_extract_filter_data($this->_request);

        $this->_set_headers();

        $this->_parse_json();
        $this->_parse_xml();
    }

    /**
     * Set request headers
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return void
     */
    private function _set_headers(): void {

        $this->_headers = [];

        $non_http = [
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'CONTENT_MD5',
        ];

        $server = $this->server();
        if (!empty($server)) foreach ($server as $key => $value) {

            $is_http = strpos($key, 'HTTP_') === 0;
            if ($is_http || in_array($key, $non_http)) {

                if ($is_http) {
                    $key = substr($key, 5);
                }

                $name = implode('-',
                    array_map('ucfirst',
                        explode('_', $key)
                    )
                );

                $this->_headers[$name] = $value;
            }
        }
    }

    /**
     * Parse and set JSON data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return void
     */
    private function _parse_json(): void {

        $content_type = strtolower($this->_headers['content-type'] ?? '');

        if (strpos($content_type, 'application/json') === false) {
            return;
        }

        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return;
        }

        $parsed = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->_json = $parsed;
        }
    }

    /**
     * Parse and set XML data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return void
     */
    private function _parse_xml(): void {

        $content_type = strtolower($this->_headers['content-type'] ?? '');

        if (strpos($content_type, 'application/xml') === false && strpos($content_type, 'text/xml') === false) {
            return;
        }

        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return;
        }

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml !== false) {
            $this->_xml = json_decode(json_encode($xml), true);
        }

        libxml_clear_errors();
    }

    /**
     * Get the ID of the request
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     */
    public function generate_id() : void {

        try {
            $this->_id = bin2hex(random_bytes(12));
        } catch (\Throwable $e) {
            $this->_id = '';
        }
    }

    /**
     * Get ID of the request
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return string
     */
    public function get_id(): string {
        return $this->_id;
    }

    /**
     * Parse the request URI
     *
     * @author TB
     * @date 29.4.2025
     *
     * @return void
     */
    private function _parse_uri(): void {

        $uri = $this->get('action_query') ?? '';

        $invalid = strcspn($uri, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-/.:;=');
        if ($invalid === 0) {
            $this->_request_uri = trim($uri, '/');
        } else {
            $this->_request_uri = '';
        }

        $this->erase('action_query'); // do not want that further, it would be in the way
    }

    /**
     * Get the request URI
     *
     * @author TB
     * @date 29.4.2025
     *
     * @return string
     */
    public function get_request_uri(): string {
        return $this->_request_uri;
    }

    /**
     * Get a GET value
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(?string $key = null, mixed $default = null): mixed {

        if (is_null($key)) {
            return $this->_get;
        } else {
            return $this->_get[$key] ?? $default;
        }
    }

    /**
     * Get a POST value
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function post(?string $key = null, mixed $default = null): mixed {

        if (is_null($key)) {
            return $this->_post;
        } else {
            return $this->_post[$key] ?? $default;
        }
    }

    /**
     * Get a REQUEST value
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function request(?string $key = null, mixed $default = null): mixed {

        if (is_null($key)) {
            return $this->_request;
        } else {
            return $this->_request[$key] ?? $default;
        }
    }

    /**
     * Get a FILE value
     *
     * @author TB
     * @date 26.4.2025, 28.5.2025
     *
     * @param string $key
     *
     * @return null|UploadedFile|array
     */
    public function file(string $key): null|UploadedFile|array {

        if (!isset($this->_files[$key]) || empty($this->_files[$key])) {
            return null;
        }

        return UploadedFile::from_request($this->_files[$key]);
    }

    /**
     * Get a SERVER value
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function server(?string $key = null, mixed $default = null): mixed {

        if (is_null($key)) {
            return $this->_server;
        } else {
            return $this->_server[$key] ?? $default;
        }
    }

    /**
     * Get a filter value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function filter(?string $key = null, mixed $default = null) : mixed {

        if (is_null($key)) {
            return $this->_filter_data;
        } else {
            return $this->_filter_data[$key] ?? $default;
        }
    }

    /**
     * Get a header value
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public function header(?string $key = null): mixed {

        if (is_null($this->_headers)) {
            $this->_set_headers();
        }

        if (is_null($key)) {
            return $this->_headers;
        } else {
            return $this->_headers[$key] ?? null;
        }
    }

    /**
     * Get JSON value
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function json(?string $key = null, mixed $default = null): mixed {

        if (is_null($this->_json)) {
            $this->_parse_json();
        }

        if (is_null($key)) {
            return $this->_json;
        } else {
            return $this->_json[$key] ?? $default;
        }
    }

    /**
     * Get XML value
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function xml(?string $key = null, mixed $default = null): mixed {

        if (is_null($this->_xml)) {
            $this->_parse_xml();
        }

        if (is_null($key)) {
            return $this->_xml;
        } else {
            return $this->_xml[$key] ?? $default;
        }
    }

    /**
     * Get the request URI
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return string
     */
    public function get_uri(): string {
        return $this->server('REQUEST_URI', '/') ?? '';
    }

    /**
     * Check if the request method is POST
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return bool
     */
    public function is_post(): bool {
        return strtoupper($this->server('REQUEST_METHOD', 'GET')) === 'POST';
    }

    /**
     * Check if the request method is GET
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return bool
     */
    public function is_get(): bool {
        return strtoupper($this->server('REQUEST_METHOD', 'GET')) === 'GET';
    }

    /**
     * Get submit value for given action
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $action Action name
     *
     * @return mixed
     */
    public function get_submit(string $action): mixed {

        $submit_name = 'submit-' . strtolower($action);

        return $this->post($submit_name);
    }

    /**
     * Erase a value from GET data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $key
     *
     * @return void
     */
    public function erase_get(string $key): void {
        unset($this->_get[$key]);
        unset($_GET[$key]);
    }

    /**
     * Erase a value from POST data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $key
     *
     * @return void
     */
    public function erase_post(string $key): void {
        unset($this->_post[$key]);
        unset($_POST[$key]);
    }

    /**
     * Erase a value from REQUEST data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $key
     *
     * @return void
     */
    public function erase_request(string $key): void {
        unset($this->_request[$key]);
        unset($_REQUEST[$key]);
    }

    /**
     * Erase a value from both GET and POST data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $key
     *
     * @return void
     */
    public function erase(string $key): void {
        $this->erase_get($key);
        $this->erase_post($key);
        $this->erase_request($key);
    }

    /**
     * Get client IP address
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public static function ip(): string {

        $server = filter_var_array($_SERVER, [
            'HTTP_X_FORWARDED_FOR' => FILTER_SANITIZE_SPECIAL_CHARS,
            'REMOTE_ADDR'          => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $addr = explode(",", $server['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0] ?? '');
        }

        return $server['REMOTE_ADDR'] ?? '';
    }

    /**
     * Extract filter data from given source
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param array $source
     *
     * @return void
     */
    private function _extract_filter_data(array &$source): void {

        foreach ($source as $key => $value) {

            if (str_starts_with($key, 'filterval-')) {

                $filter_key = substr($key, strlen('filterval-'));
                $this->_filter_data[$filter_key] = $value;

                unset($source[$key]);
            }
        }
    }

    /**
     * Set given data to filter data
     *
     * @author TB
     * @date 5.5.2025
     *
     * @param array $data
     *
     * @return void
     */
    public function set_filter_data(array $data): void {

        foreach ($data as $key => $value) {
            $this->_filter_data[$key] = $value;
        }
    }

    /**
     * Replace filter data with given data
     *
     * @author TB
     * @date 5.5.2025
     *
     * @param array $data
     *
     * @return void
     */
    public function replace_filter_data(array $data): void {
        $this->_filter_data = $data;
    }

    /**
     * Set current page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param mixed $page
     *
     * @return void
     */
    public function set_page(mixed $page): void {
        $this->_page = $page;
    }

    /**
     * Set sorting
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param Sorting|null $sorting
     *
     * @return void
     */
    public function set_sorting(?Sorting $sorting): void {
        $this->_sorting = $sorting;
    }

    /**
     * Get current page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return mixed
     */
    public function get_page(): mixed {
        return $this->_page;
    }

    /**
     * Get sorting
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return Sorting|null
     */
    public function get_sorting(): ?Sorting {
        return $this->_sorting;
    }

    /**
     * Check if GET data has given key
     *
     * @author TB
     * @date 12.5.2025
     *
     * @param string $key
     *
     * @return bool
     */
    public function has_get(string $key): bool {
        return isset($this->_get[$key]);
    }

    /**
     * Check if POST data has given key
     *
     * @author TB
     * @date 12.5.2025
     *
     * @param string $key
     *
     * @return bool
     */
    public function has_post(string $key): bool {
        return isset($this->_post[$key]);
    }

    /**
     * Check if REQUEST data has given key
     *
     * @author TB
     * @date 12.5.2025
     *
     * @param string $key
     *
     * @return bool
     */
    public function has_request(string $key): bool {
        return isset($this->_request[$key]);
    }

    /**
     * Check if filter data has given key
     *
     * @author TB
     * @date 12.5.2025
     *
     * @param string $key
     *
     * @return bool
     */
    public function has_filter(string $key): bool {
        return isset($this->_filter_data[$key]);
    }

    /**
     * Check if request expects JSON
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return bool
     */
    public function expects_json(): bool {
        return strpos($this->header('Accept') ?? '', 'application/json') !== false;
    }

    /**
     * Check if request expects XML
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return bool
     */
    public function expects_xml(): bool {
        return strpos($this->header('Accept') ?? '', 'application/xml') !== false ||
               strpos($this->header('Accept') ?? '', 'text/xml') !== false;
    }

    /**
     * Check if request is AJAX
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return bool
     */
    public function is_ajax(): bool {
        return ($this->header('X-Requested-With') ?? '') === 'XMLHttpRequest';
    }

}
