<?php
/**
 * Constants for the application
 *
 */



// application name
const APP_NAME = 'Schema Bridge';
const APP_NAME_PUBLIC = 'Schema Bridge';

// encryption
const SSL_ENC_KEY = 'e18020c9bebc23f59f3e2353f3558fdf1b0eb25193c278f4deeb6f603b155c66d2775aaa000e4eba';
const ENCRYPTION_SALT = 'ddz529budxsf8261nidglaez7xi0cyg6';

// environment
const LUMIO_DEV = 'dev';
const LUMIO_TEST = 'test';
const LUMIO_PROD = 'prod';

// assets cache control
const CACHE_VERSION = '202509021210';

// routing
$host = retrieve_host();
define('LUMIO_HOST', $host);






























/**
 * Retrieve the current host - including scheme, host (spoofing protected) and port
 *
 * @return string
 */
function retrieve_host(): string
{

    $config_file = dirname(__DIR__) . '/config/routing.php';
    if (!file_exists($config_file)) {
        http_response_code(500);
        echo 'Missing routing.php config file! Please copy from routing-template.php';
        exit;
    }

    $config = require $config_file;
    $host   = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? '';
    $scheme = filter_input(INPUT_SERVER, 'REQUEST_SCHEME') ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');
    $host   = strtolower(trim($host));
    $scheme = strtolower(trim($scheme));
    $port = (int) ($_SERVER['SERVER_PORT'] ?? 80);
    $default_ports = ['http' => 80, 'https' => 443];
    $port_append = '';
    if (!isset($default_ports[$scheme]) && strpos($host, ':') === false) {
        $port_append = ':' . $port;
    }

    $full_host = $scheme . '://' . $host . $port_append;

    // Whitelist check
    $allowed_hosts = array_filter($config['allowed_hosts'] ?? []);
    if (!empty($allowed_hosts) && !in_array($host, $allowed_hosts, true) && !in_array('forge', $GLOBALS['script_arguments'] ?? [], true)) {
        http_response_code(403);
        echo 'Host "' . $host . '" not allowed';
        exit;
    }

    return $full_host;
}








