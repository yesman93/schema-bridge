<?php
/**
 * Application initialization
 *
 * @package Lumio
 */



// Environment config file check and inclusion
$env_file = dirname(__DIR__) . '/../config/init.php';
if (!file_exists($env_file)) {

    http_response_code(500);
    echo 'Missing init.php config file! Please copy from init-template.php';
    exit;
}

$env = require $env_file;



/**
 * Check if the application is running in development environment
 *
 * @author TB
 * @date 6.5.2025
 *
 * @return bool
 */
function __is_dev(): bool {
    return LUMIO_ENV === 'dev';
}

/**
 * Check if the application is running in test environment
 *
 * @author TB
 * @date 6.5.2025
 *
 * @return bool
 */
function __is_test(): bool {
    return LUMIO_ENV === 'test';
}

/**
 * Check if the application is running in production environment
 *
 * @author TB
 * @date 6.5.2025
 *
 * @return bool
 */
function __is_prod(): bool {
    return LUMIO_ENV === 'prod';
}








// defining the environment based on the config
define("LUMIO_ENV", $env['env'] ?? LUMIO_DEV);








// Set error reporting based on the environment
if (__is_dev()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} elseif (__is_test()) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}






