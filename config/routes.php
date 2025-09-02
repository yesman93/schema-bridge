<?php
/**
 * Custom routes
 *
 * @package Lumio
 *
 */




return [

    '/^upload/' => array(
        'link' => '/upload/',
        'controller' => 'upload',
        'action' => 'index',
    ),

    '/^mapping/' => array(
        'link' => '/mapping/',
        'controller' => 'mapping',
        'action' => 'index',
    ),

];






