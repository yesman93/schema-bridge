<?php
/**
 * Application configuration file
 *
 * @package Lumio
 *
 */


return [

    'app_name' => 'Schema Bridge',

    'default_timezone' => 'Europe/Prague',                      // default timezone

    'routing' => [
        'default_controller' => 'page',
        'default_action' => 'index',
        'default_page_action' => 'resolve_page',
        'path_controllers' => dirname(__DIR__) . '/app/Controllers', // location of controllers
        'realms' => [           // realms are used to group controllers for e.g. ajax requests, system tools etc.

            // system stuff - required for the app to function properly
            'lumio' => [
                'implicit_model' => false, // whether to instantiate corresponding model automatically and connect to database
                'namespace' => 'lumio', // namespace for controller - "lumio" results in instantiating controllers like App\Controllers\Lumio\PageController
            ],

            // for AJAX requests - required for the app to function properly
            'ajax' => [
                'implicit_model' => false, // whether to instantiate corresponding model automatically and connect to database
                'namespace' => 'ajax', // namespace for controller - "ajax" results in instantiating controllers like App\Controllers\Ajax\PageController',
            ],

            // more custom realms can be added ...
        ],
    ],

    'logging' => [
        'path_logs' => dirname(__DIR__) . '/logs', // directory containing all logs
        'lock_retries' => 10, // number of retries to write into locked log file
        'size_threshold' => 10, // size threshold in MB for log file archiving
        'channels' => [
            'app' => 'app.log',
            'auth' => 'auth.log',
            'request' => 'request.log',
            'db' => 'database.log',
            'cron' => 'cron.log',
            'scaffoldr' => 'scaffoldr.log',
        ],
        'reader_cache_name' => '__logs_cache', // name of the log cache for log reader
    ],

    'pagination' => [
        'per_page'                  => 50,                                                    // number of records retrieved per page
    ],

    'filter' => [
        'fields_prefix'             => 'filterval-',                                                // prefix for the filter form fields and value keys to set them into filter data array in model
        'path_filters'              => dirname(__DIR__) . '/app/Views/partials/filters',      // location of filter templates
    ],

    'storage' => [
        'path_private' => dirname(__DIR__) . '/storage',
        'path_public' => dirname(__DIR__) . '/www/storage',
        'files_max_age' => 60 * 60 * 24 * 30, // maximum age of files in directories, that are being cleaned up (default 30 days)
        'private' => [
            'uri_show_file' => '/lumio/file/show', // URI for showing files - will be appended to current host, and file token will be appended to the URI
            'token_lifetime' => 60, // lifetime of file token in seconds (default 60 seconds)
            'session' => '__lfls', // session key for private file tokens and paths storage
            'filepath_placeholder' => dirname(__DIR__) . '/www/assets/images/404-not-found.jpg', // path to the broken file image placeholder
        ],
    ],

    'view' => [
        'path_pages'                => dirname(__DIR__) . '/app/Views/pages',     // view templates location
        'path_master_pages'         => dirname(__DIR__) . '/app/Views',
        'path_partials'             => dirname(__DIR__) . '/app/Views/partials',
        'minify_html'               => false,
        'flash_messages_session'    => '__lumio_flash',                 // session key for flash messages
        'components' => [
            'form' => [
                'default_select_plugin' => 'choices', // supported: "select2", "choices" or "" (empty string) for no plugin
                'select_plugin_classes' => [
                    'select2' => 'select2-select',
                    'choices' => 'choices-select',
                ],
                'select_plugin_required' => [
                    'select2' => ['select2', 'jquery'], // select2 relies on jquery
                    'choices' => ['choices'],
                ],
            ],
        ],
        'path_assets'               => dirname(__DIR__) . '/www/assets',
        'path_assets_public'        => '/assets',
        'assets' => [
            'css' => [
                'ext/bootstrap-5.3.5/bootstrap.min',
                'ext/jquery-ui-1.14.1/jquery-ui.min',
                'ext/flatpickr-4.6.13/flatpickr.min', // recomended to enable only if used
                'ext/select2-4.1.0/select2.min', // recomended to enable only if used
                'ext/fontawesome/css/all.min',
                'ext/choices-11.1.0/choices.min', // recomended to enable only if used
                'classes/progressbar',
                'lumio',
                'nav',
                'form',
                'list-view',
                'log-view',
                'style',
            ],
            'js' => [
                'ext/jquery-3.7.1/jquery.min',
                'ext/bootstrap-5.3.5/bootstrap.bundle.min',
                'ext/jquery-ui-1.14.1/jquery-ui.min',
                'ext/flatpickr-4.6.13/flatpickr.min', // recomended to enable only if used
                'ext/flatpickr-4.6.13/l10n/cs', // recomended to enable only if used
                'ext/select2-4.1.0/select2.full.min', // recomended to enable only if used
                'ext/select2-4.1.0/l18n/cs', // recomended to enable only if used
                'ext/choices-11.1.0/choices.min', // recomended to enable only if used
                'classes/basic',
                'classes/cookie',
                'classes/progressbar',
                'classes/modalio',
                'lumio',
                'common',
                'form',
                'filter',
                'listview',
                'logview',
                'toast',
                'nav',
            ],
            'Public' => [
                'css' => [
                    // add CSS files here
                ],
                'js' => [
                    // add JS files here
                ],
            ],
            'Private' => [
                'css' => [
                    // add CSS files here
                ],
                'js' => [
                    // add JS files here
                ],
            ],
            'Modal' => [
                'css' => [
                    // add CSS files here
                ],
                'js' => [
                    'modal',
                ],
            ],
        ],
    ],

];




