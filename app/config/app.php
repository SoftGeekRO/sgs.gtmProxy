<?php

return [
    'App' => [
        'baseUrl' => 'https://proxy.tachogeek.ro',
        'siteName' => 'SoftGeek Proxy',
        'applicationName' => 'GTM Proxy',
        'author' => 'SoulRaven',
        'namespace' => 'PROXY',
        'absolutePath' => false,
    ],
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN), // Set to false in production
    'middleware' => [
        //\SGS\Middleware\ErrorMiddleware::class
    ]];