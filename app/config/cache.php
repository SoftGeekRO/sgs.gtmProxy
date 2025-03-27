<?php

return [
    'cache' => [
        'driver' => 'file', // Options: file, memory, redis
        'file' => [
            'path' => CACHE,
        ],
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
            'password' => ''
        ]
    ]
];