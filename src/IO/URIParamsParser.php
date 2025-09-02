<?php

namespace Lumio\IO;

use Lumio\DTO\Model\Sorting;
use Lumio\Model\BaseModel;

class URIParamsParser {

    /**
     * Prefix for array keys in filter
     *
     * @author TB
     * @date 5.5.2025
     *
     * @var string
     */
    private const string _ARRAY_PREFIX = '~';

    /**
     * Separator for key-value pairs in filter
     *
     * @author TB
     * @date 5.5.2025
     *
     * @var string
     */
    private const string _SEPARATOR_KV = ':';

    /**
     * Separator for pairs in filter
     *
     * @author TB
     * @date 5.5.2025
     *
     * @var string
     */
    private const string _SEPARATOR_PAIR = ';';

    /**
     * Separator for column-direction pairs in ordering
     *
     * @author TB
     * @date 5.5.2025
     *
     * @var string
     */
    private const string _SEPARATOR_SORTING = '-';

    /**
     * Parse segments from URI, recognize page, filters and sorting, and apply it to the request object
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param array $params (by reference)
     * @param Request $request (by reference)
     *
     * @return void
     */
    public static function parse(array &$params, Request &$request): void {

        $page = 1;
        $filter_data = [];
        $sorting = null;

        $new_params = [];
        $found_filter = false;
        $found_order = false;
        foreach ($params as $index => $param) {

            // page
            if (strpos($param, 'page-') === 0) {

                $page = substr($param, 5);
                if ($page !== '' && ctype_digit($page)) {
                    $page = (int)$page;
                    continue;
                }
            }


            // filter
            if (!$found_filter && self::detect_filter($param)) {

                $filter_raw = trim($param, '/');
                $pairs = explode(self::_SEPARATOR_PAIR, $filter_raw);
                foreach ($pairs as $pair) {

                    if (!$pair || strpos($pair, self::_SEPARATOR_KV) === false) {
                        continue;
                    }

                    [$raw_key, $value] = explode(self::_SEPARATOR_KV, $pair, 2);

                    if ($raw_key === '' || $value === '') {
                        continue;
                    }

                    $is_array = $raw_key[0] === self::_ARRAY_PREFIX;
                    $key = $is_array ? substr($raw_key, 1) : $raw_key;

                    $key = rawurldecode($key);
                    $value = rawurldecode($value);

                    if (!$request->has_filter($key)) {

                        if ($is_array) {
                            $filter_data[$key][] = $value;
                        } else {
                            $filter_data[$key] = $value;
                        }
                    }
                }

                $found_filter = true;
                continue;
            }

            // sorting
            if (!$found_order && self::detect_sorting($param)) {

                [$column, $direction] = explode(self::_SEPARATOR_SORTING, $param, 2);
                $sorting = self::_sanitize_sorting($column, $direction);

                $found_order = true;
                continue;
            }

            // real business parameter
            $new_params[] = $param;
        }

        $params = $new_params;
        unset($new_params);

        $request->set_page($page);

        if (!empty($filter_data)) {
            $request->set_filter_data($filter_data);
        }

        if ($sorting !== null) {
            $request->set_sorting($sorting);
        }
    }

    /**
     * Build filter URI string from given filter data
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param array $filter_data
     *
     * @return string
     */
    public static function build_filter(array $filter_data): string {

        if (empty($filter_data)) {
            return '';
        }

        $parts = [];
        foreach ($filter_data as $key => $value) {

            if (is_array($value)) {

                foreach ($value as $v) {
                    $parts[] = self::_ARRAY_PREFIX . urlencode($key) . self::_SEPARATOR_KV . urlencode($v);
                }

            } else {
                $parts[] = urlencode($key) . self::_SEPARATOR_KV . urlencode($value);
            }
        }

        return implode(self::_SEPARATOR_PAIR, $parts);
    }

    /**
     * Build sorting URI string from given sorting object
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param Sorting|null $sorting
     *
     * @return string
     */
    public static function build_sorting(?Sorting $sorting): string {

        if (!$sorting || !$sorting->get_column() || !$sorting->get_direction()) {
            return '';
        }

        return $sorting->get_column() . self::_SEPARATOR_SORTING . $sorting->get_direction();
    }

    /**
     * Build base URI for the given controller, action and optional realm
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $controller
     * @param string $action
     * @param string|null $realm
     *
     * @return string
     */
    public static function build_base_uri(string $controller, string $action, ?string $realm = null) : string {

        $uri_parts = [];

        if (!empty($realm)) {
            $uri_parts[] = $realm;
        }

        $uri_parts[] = $controller;
        $uri_parts[] = $action;

        return '/' . implode('/', $uri_parts);

    }

    /**
     * Build URI based on the given params
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $controller
     * @param string $action
     * @param Request $request
     * @param string|null $realm
     * @param array|null $params
     *
     * @return string
     */
    public static function build(
        string $controller,
        string $action,
        Request $request,
        ?string $realm = null,
        ?array $params = null,
        ?array $filter_data = null,
        ?Sorting $sorting = null
    ): string {

        $uri_parts = [];

        $uri_parts[] = self::build_base_uri($controller, $action, $realm);

        $uri_parts[] = 'page-' . ($request->get_page() ?? 1);

        $filter_fragment = self::build_filter($filter_data ?? $request->filter());
        if ($filter_fragment !== '') {
            $uri_parts[] = $filter_fragment;
        }

        $sorting_fragment = self::build_sorting($sorting ?? $request->get_sorting());
        if ($sorting_fragment !== '') {
            $uri_parts[] = $sorting_fragment;
        }

        $params = array_filter($params ?? []);
        if (!empty($params)) {
            $uri_parts[] = implode('/', $params);
        }

        return implode('/', $uri_parts);
    }

    /**
     * Detect if given string is a filter string - contains at least one filter key-value pair
     *
     * @author TB
     * @date 5.5.2025
     *
     * @param string $string
     *
     * @return bool
     */
    public static function detect_filter(string $string) : bool {

        if (empty($string)) {
            return false;
        }

        $colon_pos = strpos($string, ':');
        $key = substr($string, 0, $colon_pos);
        $val = substr($string, $colon_pos + 1);

        return $colon_pos !== false && $key !== '' && $val !== '';
    }

    /**
     * Detect if given string is a sorting string - contains at least one column-direction pair
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $string
     *
     * @return bool
     */
    public static function detect_sorting(string $string) : bool {

        if (empty($string)) {
            return false;
        }

        $colon_pos = strpos($string, self::_SEPARATOR_KV);
        $dash_pos = strpos($string, self::_SEPARATOR_SORTING);
        $key = substr($string, 0, $dash_pos);
        $val = substr($string, $dash_pos + 1);

        return $colon_pos === false && $dash_pos !== false && $key !== '' && in_array(strtolower($val), [Sorting::ASC, Sorting::DESC], true);
    }

    /**
     * Sanitize sorting.
     * Check if the column name is valid and does not contain any potentially dangerous characters and the direction is either ASC or DESC
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $column
     * @param string $direction
     *
     * @return Sorting|null
     */
    public static function _sanitize_sorting(string $column, string $direction): ?Sorting {

        $direction = strtolower($direction);
        if ($direction !== Sorting::ASC && $direction !== Sorting::DESC) {
            return null;
        }

        // Fast character check — only allow a–z, A–Z, 0–9, underscore, dot
        $len = strlen($column);
        for ($i = 0; $i < $len; $i++) {

            $c = $column[$i];
            if (
                !(
                    ($c >= 'a' && $c <= 'z') ||
                    ($c >= 'A' && $c <= 'Z') ||
                    ($c >= '0' && $c <= '9') ||
                    $c === '_' || $c === '.'
                )
            ) {
                return null;
            }
        }

        return new Sorting($column, $direction);
    }


}
