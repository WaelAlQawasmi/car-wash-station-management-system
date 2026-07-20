<?php

return [
    'app_name' => 'Car Stashen ERP',
    'env' => getenv('APP_ENV') ?: 'local',
    'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost/car-stashen',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'dcteam_car',
        'user' => getenv('DB_USER') ?: 'dcteam_dcteam',
        'pass' => getenv('DB_PASS') ?: 'VpHqYKuT45cbV',
    ],
];
