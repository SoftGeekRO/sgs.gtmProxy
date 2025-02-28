<?php

return [
    'debug' => true, // Set to false in production
    'logs' => [
        'info' => [
            'file' => __DIR__ . '/../logs/info.log',
            'format' => 'text', // Options: 'text', 'json'
        ],
        'debug' => [
            'file' => __DIR__ . '/../logs/debug.log',
            'format' => 'text',
        ],
        'warning' => [
            'file' => __DIR__ . '/../logs/warning.log',
            'format' => 'json',
        ],
        'error' => [
            'file' => __DIR__ . '/../logs/error.log',
            'format' => 'text',
        ],
        'fatal' => [
            'file' => __DIR__ . '/../logs/fatal.log',
            'format' => 'json',
        ],
    ],
    'middleware' => [
        //\SGS\Middleware\ErrorMiddleware::class
    ],
    'routes' => [
        '/gtm/' => [
            'controller' => SGS\Controllers\GTM::class,
            'method' => 'index',
            'middleware' => [], // Optional: Route-specific middleware
        ],
        '/gtm/gtm.js' => [
            'controller' => SGS\Controllers\GTM::class,
            'method' => 'index',
            'middleware' => [], // Optional: Route-specific middleware
        ],
        '/gtm/gtm.php' => [
            'controller' => SGS\Controllers\GTM::class,
            'method' => 'index',
            'middleware' => [], // Optional: Route-specific middleware
        ],
        '/gtm/analytics.js' => [
            'controller' => SGS\Controllers\Analytics::class,
            'method' => 'index',
            'middleware' => [], // Optional: Route-specific middleware
        ],
    ],
];