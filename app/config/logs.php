<?php

return [
    'logs' => [ // most of the config is present inside the defaults.php file. You can overwrite any of the default settings from here
        'default' => ['file', 'syslog'], // Multiple default log handlers
        'level' => 'debug', // Minimum log level: debug, info, warning, error, critical,
        'handlers' => [
            'file' => [
                'format' => 'standard',
            ]
        ]
    ]
];
