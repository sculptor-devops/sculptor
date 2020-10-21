<?php

use Sculptor\Agent\Enums\DaemonGroupType;

return [
    'domains' => [
        'state-machine' => true
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'mysql',
        'username' => 'root',
        'password' => 'password'
    ],

    'php' => [
        'version' => env('PHP_VERSION', '7.4')
    ],

    'security' => [
        'password' => [
            'min' => 10,
            'max' => 20
        ]
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
    ],

    'monitors' => [
        'rotate' => 60,
        'disks' => [
            [
                'root' => env('MONITOR_DISK_ROOT', '/'),
                'device' => env('MONITOR_DISK_DEVICE', 'sda')
            ]
        ]
    ]
];
