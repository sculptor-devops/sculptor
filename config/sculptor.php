<?php

use Sculptor\Agent\Enums\DaemonGroupType;

return [
    'domains' => [
        'state-machine' => true
    ],

    'database' => [
        'default' => env('SERVER_DATABASE_DRIVER', 'mysql'),

        'drivers' => [
            'mysql' => [
                'driver' => env('MYSQL_DATABASE_DRIVER', 'mysql'),
                'host' => env('MYSQL_DATABASE_HOST', '127.0.0.1'),
                'port' => env('MYSQL_DATABASE_PORT', '3306'),
                'database' => env('MYSQL_DATABASE_NAME', 'mysql'),
                'username' => env('MYSQL_DATABASE_USERNAME', 'root'),
                'password' => 'password'
            ]
        ]
    ],

    'php' => [
        'version' => env('PHP_VERSION', '7.4')
    ],

    'security' => [
        'password' => [
            'min' => env('SECURITY_PASSWORD_MIN', 10),
            'max' => env('SECURITY_PASSWORD_MAX', 20)
        ]
    ],

    'services' => [
        DaemonGroupType::DATABASE => [
            env('SERVER_DATABASE_DRIVER', 'mysql')
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
        'rotate' => env('MONITOR_ROTATE', 60),
        'disks' => [
            [
                'root' => env('MONITOR_DISK_ROOT', '/'),
                'device' => env('MONITOR_DISK_DEVICE', 'sda')
            ]
        ]
    ],

    'backup' => [
        'archive' => env('BACKUP_ARCHIVE', 'local'),
        'temp' => env('BACKUP_TMP', '/tmp'),
        'compression' => env('BACKUP_COMPRESSION', 'zip'),

        'drivers' => [
            'default' => 'local',

            'local' => [
                'path' => env('BACKUP_LOCAL_PATH', SCULPTOR_HOME . '/backups'),
            ],

            's3' => [
                'key' => env('S3_KEY', 'key'),
                'secret' => env('S3_SECRET', 'secret'),
                'region' => env('S3_REGION', 'region'),
                'endpoint' => env('S3_END_POINT', 'url'),
                'bucket' => env('S3_BUCKET', 'bucket')
            ]
        ]
    ]
];
