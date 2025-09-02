<?php
/**
 * Routing configuration, that varies in environments
 *
 * @package Lumio
 *
 */




return [

    'history' => [
        'enabled' => true, // enable history
        'cookie' => '__lhist', // cookie name for history
        'expiration' => 2592000, // cookie expiration in seconds (2592000 s = 30 days)
        'max_entries' => 20, // maximum number of history entries - if size of JSON history exceeds cookie max size, the oldest entries exceeding this limit will be removed
    ],

    'allowed_hosts' => [
        'schema-bridge.test',
        // allowed hosts go here
    ],

];





