<?php

/**
 * Debug function to dump variables in a styled box
 *
 * @param mixed $var
 * @param bool $exit
 *
 * @return void
 */
function vdump(mixed $var, bool $exit = false): void {

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? null;
    $file = $backtrace['file'] ?? 'Unknown file';
    $line = $backtrace['line'] ?? 'Unknown line';

    echo '<div style="
            background: #f5f5f5;
            color: #333;
            padding: 25px 10px 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: Consolas, monospace;
            font-size: 14px;
            overflow-x: auto;
            max-width: 90vw;
            position: relative;
        ">';

    echo '<div style="
            position: absolute;
            top: 5px;
            left: 10px;
            font-size: 10px;
            color: #777;
        ">';
    echo htmlspecialchars($file) . ' : ' . $line;
    echo '</div>';

    echo '<pre style="margin:0;">';
    var_dump($var);
    echo '</pre>';

    echo '</div>';

    if ($exit) {
        exit;
    }
}

/**
 * Print localized text (for use inside HTML, etc.)
 *
 * @param string $text
 * @param mixed ...$args
 *
 * @return void
 */
function __t(string $text, mixed ...$args): void {
    echo __tx($text, ...$args);
}

/**
 * Return localized text (for use inside variables, HTML building, etc.)
 *
 * @param string $text
 * @param mixed ...$args
 *
 * @return string
 */
function __tx(string $text, mixed ...$args): string {

    // In future, translation logic will be here (e.g., language array, db, file)
    $translated = $text; // currently passthrough

    if (!empty($args)) {
        return vsprintf($translated, $args);
    }

    return $translated;
}

/**
 * Check if a variable is a non-empty array
 *
 * @param mixed $array
 *
 * @return bool
 */
function is_nempty_array($array) : bool {
    return is_array($array) && !empty($array);
}

/**
 * Get the depth of a multi-dimensional array
 *
 * @param array $array
 *
 * @return int
 */
function array_depth(array $array): int {

    if (empty($array)) {
        return 0;
    }

    $max_depth = 1;

    foreach ($array as $value) {

        if (is_array($value)) {

            $depth = array_depth($value) + 1;
            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}

/**
 * Get simplified backtrace
 *
 * @param bool $as_text
 * @param array|null $trace
 *
 * @return string|array
 */
function get_backtrace_simply(bool $as_text = false, ?array $trace = null) : string|array {

    $trace = $trace ?? debug_backtrace();
    $result = [];

    foreach ($trace as $row) {

        $function = $row['function'] ?? '';
        $class = $row['class'] ?? '';
        $type = $row['type'] ?? '';
        $args = $row['args'] ?? [];
        $file = $row['file'] ?? '';
        $line = $row['line'] ?? '';

        $args_str = '';

        foreach ($args as $arg) {
            $args_str .= empty($args_str) ? '' : ', ';

            if (is_bool($arg)) {
                $args_str .= $arg ? 'true' : 'false';
            } elseif (is_string($arg)) {
                $args_str .= "'$arg'";
            } elseif (is_array($arg)) {
                $args_str .= _format_array_arg($arg);
            } elseif (is_object($arg)) {
                $args_str .= '[Object]';
            } else {
                $args_str .= (string)$arg;
            }
        }

        $function_call = ($class ? $class . $type : '') . $function . '(' . $args_str . ')';
        $entry = '<span style="display:inline-block;margin-bottom:5px"><small>' . $file . ':' . $line . '</small>&nbsp;&nbsp;&nbsp;<strong><code>' . $function_call . '</code></strong></span>';

        $result[] = $entry;
    }

    if ($as_text) {
        return '<br />' . implode('<br />', $result);
    }

    return $result;
}

/**
 * Helper for formatting array argument nicely
 *
 * @param array $array
 *
 * @return string
 */
function _format_array_arg(array $array): string {

    if (array_depth($array) > 1) {
        $output = '[Array]';
    } else {

        $output = '[' . implode(', ', array_map(static function ($item) {

            if (is_bool($item)) {
                return $item ? 'true' : 'false';
            }

            if (is_string($item)) {
                return "'$item'";
            }

            if (is_array($item)) {
                return '[Array]';
            }

            if (is_object($item)) {
                return '[Object]';
            }

            return (string)$item;

        }, $array)) . ']';
    }

    return $output;
}

/**
 * Lumio custom error handler
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 *
 * @return void
 */
function lumio_error_handler(int $errno, string $errstr, string $errfile = '', int $errline = 0, array $errcontext = []): void {

    $style_wrapper = 'background: #fff; margin-bottom: 10px; padding: 0; border-radius: 12px; overflow: hidden; font-family: Arial, sans-serif;';
    $style_table = 'width: 100%; border-collapse: collapse;';
    $style_th = 'background: #00a5ff; color: #fff; font-size: 12px; font-weight: bold; padding: 5px 12px; text-align: left;';
    $style_td_title = 'background: #eaeaea; color: #333; padding: 5px 10px; font-size: 14px; font-weight: bold; width: 70px; vertical-align: top; border-bottom: 1px solid #ddd;';
    $style_td_value = 'background: #f2f2f2; color: #333; padding: 5px 10px; font-size: 14px; border-bottom: 1px solid #ddd;';

    $backtrace = get_backtrace_simply();
    if (!empty($backtrace) && str_contains($backtrace[0], 'get_backtrace_simply')) {
        array_shift($backtrace);
    }
    if (!empty($backtrace) && str_contains($backtrace[0], 'lumio_error_handler')) {
        array_shift($backtrace);
    }

    $backtrace = implode("\n<br />", $backtrace);

    echo '
    <div style="' . $style_wrapper . '">
        <table style="' . $style_table . '">
            <tr>
                <th colspan="2" style="' . $style_th . '">Error</th>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Error #</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars((string)$errno) . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Message</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars($errstr) . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">File</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars($errfile) . ':' . (int)$errline . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Context</td>
                <td style="' . $style_td_value . '"><pre style="margin:0; font-size:0.9em;">' . htmlspecialchars(print_r($errcontext, true)) . '</pre></td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Backtrace</td>
                <td style="' . $style_td_value . '"><pre style="margin:0; font-size:0.9em;">' . $backtrace . '</pre></td>
            </tr>
        </table>
    </div>';
}

set_error_handler('lumio_error_handler');

/**
 * Lumio custom exception handler
 *
 * @param Throwable $exception
 *
 * @return void
 */
function lumio_exception_handler(Throwable $exception): void {

    $style_wrapper = 'background: #fff; margin-bottom: 10px; padding: 0; border-radius: 12px; overflow: hidden; font-family: Arial, sans-serif;';
    $style_table = 'width: 100%; border-collapse: collapse;';
    $style_th = 'background: #ff4c4c; color: #fff; font-size: 12px; font-weight: bold; padding: 5px 12px; text-align: left;';
    $style_td_title = 'background: #eaeaea; color: #333; padding: 5px 10px; font-size: 14px; font-weight: bold; width: 70px; vertical-align: top; border-bottom: 1px solid #ddd;';
    $style_td_value = 'background: #f2f2f2; color: #333; padding: 5px 10px; font-size: 14px; border-bottom: 1px solid #ddd;';

    $backtrace = get_backtrace_simply(false, $exception->getTrace());
    $backtrace = '<div>' . implode("</div><div>", $backtrace) . '</div>';

    echo '
    <div style="' . $style_wrapper . '">
        <table style="' . $style_table . '">
            <tr>
                <th colspan="2" style="' . $style_th . '">Uncaught Exception</th>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Type</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars(get_class($exception)) . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Message</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars($exception->getMessage()) . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">File</td>
                <td style="' . $style_td_value . '">' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</td>
            </tr>
            <tr>
                <td style="' . $style_td_title . '">Backtrace</td>
                <td style="' . $style_td_value . '; padding-top: 10px; line-height: 1.2"><pre style="margin:0; font-size:0.9em;">' . $backtrace . '</pre></td>
            </tr>
        </table>
    </div>';
}

set_exception_handler('lumio_exception_handler');

/**
 * Lumio failure handler
 *
 * @param string $message
 * @param mixed $code
 * @param Throwable|null $e
 *
 * @return void
 */
function lumio_fail(string $message = '', mixed $code = 0, ?Throwable $e = null): void {


    if (empty($code)) {
        http_response_code(500);
        $code = '';
    } else if (is_string($code)) {
        http_response_code(500);
    } else {
        http_response_code($code);
    }

    $trace = '';
    if (__is_dev()) {
        $trace = get_backtrace_simply(true, $e?->getTrace());
        $trace = '<div class="fw-bold">TRACE [dev]:</div>' . $trace;
    }

    ob_clean();

    ob_start();

    include './../app/Views/Error.Master.php';

    $content = ob_get_clean();

    $content = str_replace('@{message}', $message, $content);
    $content = str_replace('@{code}', $code, $content);
    $content = str_replace('@{trace}', $trace, $content);

    echo $content;

    exit;
}

/**
 * Generate a random string of specified length with configurable character sets
 *
 * @param int $length
 * @param bool $use_uppercase_only
 * @param bool $use_numbers_only
 * @param bool $require_special_chars
 *
 * @return string
 */
function random_string(int $length, bool $use_uppercase_only = false, bool $use_numbers_only = false, bool $require_special_chars = false): string {

    if ($length <= 0) {
        return '';
    }

    $chars_lower   = 'abcdefghijklmnopqrstuvwxyz';
    $chars_upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_numeric = '0123456789';
    $chars_special = '!@#$+-%&*?^_';

    $char_pool = '';

    if ($use_numbers_only) {
        $char_pool = $chars_numeric;
    } else {

        $char_pool = $chars_upper;

        if (!$use_uppercase_only) {
            $char_pool .= $chars_lower;
        }

        $char_pool .= $chars_numeric;

        if ($require_special_chars) {
            $char_pool .= $chars_special;
        }
    }

    $output = '';

    try {

        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, strlen($char_pool) - 1);
            $output .= $char_pool[$index];
        }

        // Enforce at least one special character if required and not present
        if ($require_special_chars && strpbrk($output, $chars_special) === false) {

            $insert_count = max(1, (int) ceil($length / 6)); // Insert at least 1, more for long tokens

            for ($i = 0; $i < $insert_count; $i++) {
                $pos = random_int(0, $length - 1);
                $replacement = $chars_special[random_int(0, strlen($chars_special) - 1)];
                $output[$pos] = $replacement;
            }
        }

    } catch (Exception $e) {
        return '';
    }

    return $output;
}


/**
 * Convert an array to a human-readable sentence
 *
 * @param array $array
 * @param bool $highlight
 *
 * @return string
 */
function array_to_sentence(array $array, bool $highlight = false) : string {

    if (empty($array)) {
        return '';
    }

    // Flatten and filter non-scalar values
    $array = array_values(array_filter($array, 'is_scalar'));
    $count = count($array);

    if ($count === 0) {
        return '';
    }

    // Wrap items if highlighting is enabled
    if ($highlight) {

        foreach ($array as &$item) {
            $item = '<strong>' . htmlspecialchars((string)$item, ENT_QUOTES, 'UTF-8') . '</strong>';
        }

        unset($item);

    } else {

        // Cast all items to string
        foreach ($array as &$item) {
            $item = (string)$item;
        }

        unset($item);
    }

    $and = __tx('and');

    if ($count === 1) {
        return $array[0];
    }

    if ($count === 2) {
        return $array[0] . ' ' . $and . ' ' . $array[1];
    }

    $last = array_pop($array);

    return implode(', ', $array) . ' ' . $and . ' ' . $last;
}

/**
 * Flatten a multi-dimensional array by one level
 *
 * @param array $array
 *
 * @return array
 */
function array_flatten_once(array $array): array {

    $ret = [];
    foreach ($array as $group) {

        if (is_array($group)) foreach ($group as $row) {
            $ret[] = $row;
        }
    }

    return $ret;
}

/**
 * Determines if given array is associative
 *
 * @param array $arr
 *
 * @return bool
 */
function array_is_assoc(array $arr): bool {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Re-indexes an array of associative arrays using a specific key from each row.
 *
 * If $group_rows is true, rows with the same key will be grouped together in an array.
 * If false, only the last row with a given key will be kept
 *
 * @param array $array
 * @param string $key
 * @param bool $group_rows
 *
 * @return array
 */
function array_key_by(array $array, string $key, bool $group_rows = false): array {

    $ret = [];
    foreach ($array as $row) {

        if (is_array($row) && array_key_exists($key, $row)) {

            if ($group_rows) {
                $ret[$row[$key]][] = $row;
            } else {
                $ret[$row[$key]] = $row;
            }
        }
    }

    return $ret;
}

/**
 * Generates a UUID v4 string (RFC 4122 compliant)
 *
 * @param string|null $data
 *
 * @return string UUID string on success, false on failure.
 */
function uuid_v4(?string $data = null): string {

    if ($data === null) {

        try {
            $data = random_bytes(16);
        } catch (Exception $e) {
            return '';
        }
    }

    if (strlen($data) !== 16) {
        return '';
    }

    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Extracts values from a specified column in a flat or grouped array of associative arrays (e.g. database rows)
 *
 * @param array $array
 * @param string $column
 * @param bool $accept_empty
 * @param bool $distinct
 * @param bool $is_grouped
 *
 * @return array
 */
function array_extract_column(array $array, string $column, bool $accept_empty = true, bool $distinct = false, bool $is_grouped = false): array {

    $ret = [];
    if ($is_grouped) foreach ($array as $group) {

        if (is_array($group)) foreach ($group as $row) {

            if (is_array($row) && array_key_exists($column, $row)) {
                $ret[] = $row[$column];
            }
        }

    } else foreach ($array as $row) {

        if (is_array($row) && array_key_exists($column, $row)) {
            $ret[] = $row[$column];
        }
    }

    if (!$accept_empty) {
        $ret = array_filter($ret, fn($v) => $v !== null && $v !== '' && $v !== []);
    }

    if ($distinct) {
        $ret = array_values(array_unique($ret));
    }

    return $ret;
}

/**
 * Returns uppercase initials from a given string - multibyte-safe
 *
 * @param string $string
 *
 * @return string
 */
function get_initials(string $string): string {

    $string = trim($string);
    if ($string === '') {
        return '';
    }

    $parts = array_filter(explode(' ', $string), fn($part) => $part !== '');

    $ret = '';
    foreach ($parts as $part) {
        $ret .= mb_strtoupper(mb_substr($part, 0, 1));
    }

    return $ret;
}

/**
 * Resolves IPv4 address from given URL
 *
 * @param string $url
 *
 * @return string
 */
function get_ip_by_url(string $url): string {

    if (trim($url) === '') {
        return '';
    }

    $host = parse_url($url, PHP_URL_HOST) ?: $url;

    $ip = gethostbyname($host);

    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '';
}

/**
 * Get current date and time
 *
 * @param bool $date_only
 *
 * @return string
 */
function get_now(bool $date_only = false): string {
    return $date_only ? date('Y-m-d') : date('Y-m-d H:i:s');
}

/**
 * Finds the first row where a specific key equals a given value
 *
 * @param array $rows Array of associative arrays (rows)
 * @param string $key Key to match
 * @param mixed $value Value to compare against
 *
 * @return array|null
 */
function row_by_key(array $rows, string $key, mixed $value): ?array {

    foreach ($rows as $row) {

        if (is_array($row) && array_key_exists($key, $row) && $row[$key] == $value) {
            return $row;
        }
    }

    return null;
}

/**
 * Finds all rows where a specific key equals a given value
 *
 * @param array $rows Array of associative arrays (rows)
 * @param string $key Key to match
 * @param mixed $value Value to compare against
 *
 * @return array|null
 */
function rows_by_key(array $rows, string $key, mixed $value): ?array {

    $result = [];
    foreach ($rows as $row) {

        if (is_array($row) && array_key_exists($key, $row) && $row[$key] == $value) {
            $result[] = $row;
        }
    }

    return $result !== [] ? $result : null;
}

/**
 * Returns if the given string starts with the given prefix
 *
 * @param string $string
 * @param string $prefix
 *
 * @return bool
 */
function has_prefix(string $string, string $prefix): bool {
    return strncmp($string, $prefix, strlen($prefix)) === 0;
}

/**
 * Converts a hex color string to an RGB CSS string.
 *
 * Supports both 3-digit and 6-digit hex values, with or without the leading "#"
 *
 * @param string $hex
 *
 * @return string|null
 */
function hex2rgb(string $hex): ?string {

    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
    }

    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return null;
    }

    [$r, $g, $b] = sscanf($hex, "%02x%02x%02x");

    return "$r, $g, $b";
}

/**
 * Trims trailing decimal zeroes from a number string
 *
 * @param string|float|int $number
 * @param string $decimal_separator
 *
 * @return float
 */
function minimize_decimals(string|float|int $number, string $decimal_separator = ','): float {

    if ($number === null || $number === '') {
        return 0.0;
    }

    $number = str_replace(' ', '', (string)$number);

    // Safety net
    if (strpos($number, $decimal_separator) === false) {
        return (float)str_replace($decimal_separator, '.', $number);
    }

    $number = rtrim($number, '0');

    if (substr($number, -1) === $decimal_separator) {
        $number = substr($number, 0, -1);
    }

    return (float)str_replace($decimal_separator, '.', $number);
}

/**
 * Returns the count of decimal digits in given number (after trimming trailing zeroes)
 *
 * @param string|float|int $number
 * @param string $decimal_point
 *
 * @return int
 */
function decimals_count(string|float|int $number, string $decimal_point = ','): int {

    if ($number === null || $number === '') {
        return 0;
    }

    $number = str_replace(' ', '', (string)$number);

    if (strpos($number, $decimal_point) === false) {
        return 0;
    }

    $number = rtrim($number, '0');

    if (substr($number, -1) === $decimal_point) {
        return 0;
    }

    $pos = strpos($number, $decimal_point);

    return $pos !== false ? strlen(substr($number, $pos + 1)) : 0;
}

/**
 * Replaces placeholders in the format "@{key}" in given string with values from the provided array
 *
 * @param string $string
 * @param array $vars
 *
 * @return string
 */
function populate_string(string $string, array $vars): string {

    if ($string === '' || $vars === []) {
        return $string;
    }

    $search = [];
    $replace = [];
    foreach ($vars as $key => $val) {

        if (is_scalar($val)) {
            $search[] = '@{' . $key . '}';
            $replace[] = $val;
        }
    }

    return str_replace($search, $replace, $string);
}

/**
 * Removes the UTF-8 BOM (Byte Order Mark) from the beginning of given file, if present
 *
 * @param string $file Full path to the file
 *
 * @return void
 */
function remove_bom(string $file): void {

    if ($file === '' || !file_exists($file)) {
        return;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        return;
    }

    // Only strip BOM if it's actually at the start
    if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
        $content = substr($content, 3);
        file_put_contents($file, $content);
    }
}

/**
 * Normalizes given string by removing diacritics and converting to ASCII
 *
 * @param string $string
 *
 * @return string
 */
function _normalize_string(string $string): string {

    if (function_exists('transliterator_transliterate')) {
        return transliterator_transliterate('NFD; [:Nonspacing Mark:] Remove; NFC;', $string);
    }

    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

    return $converted !== false ? $converted : $string;
}

/**
 * Removes all diacritics and applies basic normalization:
 * - Hyphens to underscores
 * - Pipes removed
 * - Spaces normalized or replaced by hyphens
 *
 * @param string $string
 * @param bool $preserve_spaces
 *
 * @return string
 */
function remove_diacritics(string $string, bool $preserve_spaces = false): string {

    if ($string === '') {
        return '';
    }

    $string = _normalize_string($string);

    $string = str_replace('-', '_', $string);
    $string = str_replace('|', '', $string);

    if ($preserve_spaces) {

        while (strpos($string, '  ') !== false) {
            $string = str_replace('  ', ' ', $string);
        }

    } else {

        $string = str_replace(["\t", "\r", "\n"], ' ', $string);
        while (strpos($string, '  ') !== false) {
            $string = str_replace('  ', ' ', $string);
        }

        $string = str_replace(' ', '-', $string);
    }

    return $string;
}

/**
 * Converts a string to a URL-safe slug:
 * - Removes diacritics
 * - Converts to lowercase
 * - Replaces whitespace, pipes, and underscores with hyphens
 * - Collapses multiple hyphens
 *
 * @param string $string
 *
 * @return string
 */
function urlify(string $string): string {

    if ($string === '') {
        return '';
    }

    $string = _normalize_string($string);
    $string = mb_strtolower($string);

    $string = str_replace([' ', "\t", "\r", "\n", '|', '_'], '-', $string);

    while (strpos($string, '--') !== false) {
        $string = str_replace('--', '-', $string);
    }

    return trim($string, '-');
}

/**
 * Fetches content from a URL using cURL (alternative to file_get_contents() for external sources)
 *
 * @param string $url
 * @param array|null $params
 *
 * @return string
 */
function url_get_contents(string $url, ?array $params = null): string {

    if (!function_exists('curl_init')) {
        return '';
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $connect_timeout = 6;
    $timeout = 10;

    if ($params !== null) {

        if (!empty($params[CURLOPT_CONNECTTIMEOUT])) {
            $connect_timeout = (int)$params[CURLOPT_CONNECTTIMEOUT];
        }

        if (!empty($params[CURLOPT_TIMEOUT])) {
            $timeout = (int)$params[CURLOPT_TIMEOUT];
        }
    }

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    $result = curl_exec($ch);

    $err_no = curl_errno($ch);
    $err_msg = curl_error($ch);

    curl_close($ch);

    if ($err_no > 0 || !empty($err_msg)) {
        error_log("cURL Error ($err_no): $err_msg");
        vdump("cURL Error ($err_no): $err_msg");
    }

    return $result;
}

/**
 * Sorts an array of associative arrays (rows) by given key and direction (quick sort based)
 *
 * @param array $array
 * @param string $key
 * @param string $direction
 *
 * @return array|null
 */
function sort_rows(array $array, string $key, string $direction = 'asc'): ?array {

    if ($array === [] || !is_array(reset($array)) || $key === '') {
        return null;
    }

    $direction = strtolower($direction);
    $is_desc = $direction === 'desc';

    usort($array, function ($a, $b) use ($key, $is_desc) {

        // treat missing keys as 0
        $val_a = $a[$key] ?? 0;
        $val_b = $b[$key] ?? 0;

        $cmp = $val_a <=> $val_b;

        return $is_desc ? -$cmp : $cmp;
    });

    return $array;
}

/**
 * Normalize the given URL path
 *
 * @param string $url
 *
 * @return string
 */
function normalize_url(string $url): string {

    $url = trim($url);
    $url = preg_replace('/\/+/', '/', $url);
    $url = ltrim($url, '/');

    return '/' . $url;
}

/**
 * Benchmark given URL by fetching it multiple times and measuring the time taken
 *
 * @param string $label
 * @param string $url
 * @param int $iterations
 *
 * @return void
 */
function benchmark(string $label, string $url, int $iterations = 100): void {

    $times = [];

    for ($i = 0; $i < $iterations; $i++) {

        $start = microtime(true);

        // You can switch to curl if needed
        $response = @url_get_contents($url);

        $elapsed = microtime(true) - $start;
        $times[] = $elapsed;
    }

    $avg = array_sum($times) / count($times);
    $max = max($times);
    $min = min($times);

    echo "Benchmark: $label<br />\n";
    echo "Iterations: $iterations<br />\n";
    echo "Average time: " . round($avg * 1000, 4) . " ms<br />\n";
    echo "Min time: " . round($min * 1000, 4) . " ms<br />\n";
    echo "Max time: " . round($max * 1000, 4) . " ms<br />\n";
    echo "-----------------------------<br />\n<br />\n";
}






















