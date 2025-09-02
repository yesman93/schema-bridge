<?php

namespace Lumio\View;

use Lumio\Config;
use Lumio\DTO\Model\Pagination;
use Lumio\DTO\View\Breadcrumb as BreadcrumbDTO;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;

abstract class Component {

    /**
     * Current realm
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string|null
     */
    protected static ?string $_realm = null;

    /**
     * Current controller
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    protected static ?string $_controller = null;

    /**
     * Current action
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    protected static ?string $_action = null;

    /**
     * Current request object
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var Request|null
     */
    protected static ?Request $_request = null;

    /**
     * Current title
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    protected static ?string $_title = null;

    /**
     * Current pagination object
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var Pagination|null
     */
    protected static ?Pagination $_pagination = null;

    /**
     * Current URI parameters
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var array|null
     */
    protected static ?array $_params = null;

    /**
     * Current master page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string|null
     */
    protected static ?string $_master = null;

    /**
     * Current CSRF token
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var string
     */
    protected static string $_csrf_token = '';

    /**
     * Current CSRF token form field name
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var string
     */
    protected static string $_csrf_field = '';

    /**
     * DTO for breadcrumb items
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var BreadcrumbDTO|null
     */
    protected static ?BreadcrumbDTO $_breadcrumb = null;

    /**
     * Set realm
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $_realm
     *
     * @return void
     */
    public static function set_realm(string $_realm): void {
        self::$_realm = $_realm;
    }

    /**
     * Set controller
     *
     * @param string $_controller
     *
     * @return void
     *@author TB
     * @date 1.5.2025
     *
     */
    public static function set_controller(string $_controller): void {
        self::$_controller = $_controller;
    }

    /**
     * Set action
     *
     * @param string $_action
     *
     * @return void
     *@author TB
     * @date 1.5.2025
     *
     */
    public static function set_action(string $_action): void {
        self::$_action = $_action;
    }

    /**
     * Set request
     *
     * @param Request $_request
     *
     * @return void
     *@author TB
     * @date 1.5.2025
     *
     */
    public static function set_request(Request $_request): void {
        self::$_request = $_request;
    }

    /**
     * Set title
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $title
     *
     * @return void
     */
    public static function set_title(string $title): void {
        self::$_title = $title;
    }

    /**
     * Set pagination
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param Pagination $pagination
     *
     * @return void
     */
    public static function set_pagination(Pagination $pagination): void {
        self::$_pagination = $pagination;
    }

    /**
     * Set URI parameters
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param array $params
     *
     * @return void
     */
    public static function set_params(array $params): void {
        self::$_params = $params;
    }

    /**
     * Set master page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string $master
     *
     * @return void
     */
    public static function set_master(string $master): void {
        self::$_master = $master;
    }

    /**
     * Get realm
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return string|null
     */
    public static function get_realm(): ?string {
        return self::$_realm;
    }

    /**
     * Get controller
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public static function get_controller(): ?string {
        return self::$_controller;
    }

    /**
     * Get action
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public static function get_action(): ?string {
        return self::$_action;
    }

    /**
     * Get request
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return Request|null
     */
    public static function get_request(): ?Request {
        return self::$_request;
    }

    /**
     * Get title
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public static function get_title(): ?string {
        return self::$_title;
    }

    /**
     * Get pagination
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return Pagination|null
     */
    public static function get_pagination(): ?Pagination {
        return self::$_pagination;
    }

    /**
     * Get URI parameters
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return array|null
     */
    public static function get_params(): ?array {
        return self::$_params;
    }

    /**
     * Get master page
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return string|null
     */
    public static function get_master(): ?string {
        return self::$_master;
    }

    /**
     * Get current action URI
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    protected static function _get_action_uri() : string {
        return URIParamsParser::build(self::$_controller, self::$_action, self::$_request, self::$_realm, self::$_params);
    }

    /**
     * Get base URI for the current action
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return string
     */
    protected static function _get_base_uri() : string {
        return URIParamsParser::build_base_uri(self::$_controller, self::$_action, self::$_realm);
    }

    /**
     * Generate HTML attributes from the given associative array
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param array $attributes
     *
     * @return string
     */
    protected function _get_attr(array $attributes): string {

        $html = '';
        foreach ($attributes as $key => $value) {

            if ($value !== null) {
                $html .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }

        return $html;
    }

    /**
     * Generate HTML attribute if the given value is not empty
     *
     * @author TB
     * @date 2.5.2025
     *
     * @param string $name
     * @param mixed $value
     *
     * @return string
     */
    protected static function _not_empty_to_attr(string $name, mixed $value): string {

        if (!empty($name) && !empty($value)) {
            return ' ' . htmlspecialchars($name) . '="' . htmlspecialchars($value) . '"';
        } else {
            return '';
        }
    }

    /**
     * Check if the given string contains an icon class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $string
     *
     * @return bool
     */
    protected static function _contains_icon_class($string) : bool {

        if (empty ($string)) {
            return false;
        }

        $is_fa = strpos($string, 'fa-') !== false;
        $is_uil = strpos($string, 'uil-') !== false;
        $is_ri = strpos($string, 'ri-') !== false;
        $is_bx = strpos($string, 'bx-') !== false;
        $is_mdi = strpos($string, 'mdi-') !== false;
        $is_la = strpos($string, 'la-') !== false;
        $is_feather = strpos($string, 'feather-') !== false;

        return $is_fa || $is_uil || $is_ri || $is_bx || $is_mdi || $is_la || $is_feather;
    }

    /**
     * Get the prefix for filter fields
     *
     * @author TB
     * @date 12.5.2025
     *
     * @return string
     */
    protected static function _filter_prefix() : string {

        try {
            $filter_prefix = Config::get('app.filter.fields_prefix');
        } catch (\Throwable $e) {
            $filter_prefix = '';
        }

        return $filter_prefix;
    }

    /**
     * Get the search query from request
     *
     * @author TB
     * @date 12.5.2025
     *
     * @return string
     */
    protected static function _search_query() : string {

        $name = self::_filter_prefix() . 'search_query';

        $value = '';

        $value_get = self::$_request->get($name);
        if (!empty($value_get)) {
            $value = $value_get;
        }

        $value_post = self::$_request->post($name);
        if (!empty($value_post)) {
            $value = $value_post;
        }

        $value_filter = self::$_request->filter('search_query');
        if (!empty($value_filter)) {
            $value = $value_filter;
        }

        return $value;
    }

    protected static function _search_uri() : string {

        $filter_data = self::$_request->filter();
        if (empty($filter_data)) {
            $filter_data = [];
        }

        unset($filter_data['search_query']);

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            filter_data: $filter_data,
        );
    }

    /**
     * Set CSRF token
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param string $token
     *
     * @return void
     */
    public static function set_csrf_token(string $token): void {
        self::$_csrf_token = $token;
    }

    /**
     * Get CSRF token form field
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param string $field
     *
     * @return void
     */
    public static function set_csrf_field(string $field): void {
        self::$_csrf_field = $field;
    }

    /**
     * Set given breadcrumb
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param BreadcrumbDTO $breadcrumb
     *
     * @return void
     */
    public static function set_breadcrumb(BreadcrumbDTO $breadcrumb): void {
        self::$_breadcrumb = $breadcrumb;
    }

}
