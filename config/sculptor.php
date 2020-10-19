<?php

use Sculptor\Agent\Enums\DaemonGroupType;

return [
    'php' => [
        'version' => env('PHP_VERSION', '7.4')
    ],

    'services' => [
        DaemonGroupType::DATABASE => [
            'mysql'
        ],
        DaemonGroupType::WEB => [
            'nginx',
            'php' . env('PHP_VERSION', '7.4') . '-fpm'
        ],
        DaemonGroupType::QUEUE => [
            'redis',
            'supervisor'
        ],
        DaemonGroupType::REMOTE => [
            'ssh',
            'fail2ban'
        ]
    ]
];
