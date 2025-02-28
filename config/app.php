<?php

return [
    'debug' => true, // Set to false in production
    'log_file' => __DIR__ . '/../logs/sgs.log', // Path to the log file
    'log_format' => 'json', // Options: 'json', 'html', 'text'
    'middleware' => [
        \SGS\Middleware\ErrorHandlerMiddleware::class
    ],
    'routes' => [
        '/abc' => [
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