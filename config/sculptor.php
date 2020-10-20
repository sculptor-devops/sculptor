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
