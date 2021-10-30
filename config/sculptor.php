<?php

use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Enums\BackupRotationType;
use Sculptor\Agent\Enums\BackupArchiveType;

use Sculptor\Agent\Backup\Subjects\Blueprint as BackupBlueprint;
use Sculptor\Agent\Backup\Subjects\Database as BackupDatabase;
use Sculptor\Agent\Backup\Subjects\Domain as BackupDomain;

use Sculptor\Agent\Backup\Rotations\Number;
use Sculptor\Agent\Backup\Rotations\Days;

use Sculptor\Agent\Backup\Archives\Local;
use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Archives\Dropbox;

use Sculptor\Agent\Monitors\System\Cpu;
use Sculptor\Agent\Monitors\System\Disk;
use Sculptor\Agent\Monitors\System\Io;
use Sculptor\Agent\Monitors\System\Memory;
use Sculptor\Agent\Monitors\System\Uptime;

return [
    'domains' => [
        'state-machine' => true
    ],

    'database' => [
        'default' => env('SERVER_DATABASE_DRIVER', 'mysql'),

        'restart_delay' => env('SERVER_DATABASE_RESTART_DELAY', 5),

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
        'version' => env('PHP_VERSION', ENGINE_VERSION)
    ],

    'security' => [
        'hash' => env('SECURITY_HASH', 'sha1'),
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
            'php' . env('PHP_VERSION', ENGINE_VERSION) . '-fpm'
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
        'drivers' => [
            Cpu::class,
            Disk::class,
            Io::class,
            Memory::class,
            Uptime::class,
        ],
        'rotate' => env('MONITOR_ROTATE', 60),
        'disks' => [
            env('MONITOR_DISK_DEVICE', 'sda') => [
                'root' => env('MONITOR_DISK_ROOT', '/')
            ]
        ]
    ],

    'backup' => [
        'archive' => env('BACKUP_ARCHIVE', 'local'),
        'rotation' => env('BACKUP_ROTATION', 'days'),
        'rotations' => [
            BackupRotationType::NUMBER => Number::class,
            BackupRotationType::DAYS => Days::class
        ],
        'temp' => env('BACKUP_TMP', '/tmp'),
        'compression' => env('BACKUP_COMPRESSION', 'zip'),

        'strategies' => [
            BackupType::DATABASE => BackupDatabase::class,
            BackupType::DOMAIN => BackupDomain::class,
            BackupType::BLUEPRINT =>BackupBlueprint::class
        ],

        'drivers' => [
            'available' => [
                BackupArchiveType::LOCAL => Local::class,
                BackupArchiveType::S3 => S3::class,
                BackupArchiveType::DROPBOX => Dropbox::class   
            ],
            'default' => 'local',

            'local' => [
                'path' => env('BACKUP_LOCAL_PATH', SCULPTOR_HOME . '/backups'),
            ],

            's3' => [
                'path' => env('S3_KEY_PATH', 'backups'),
                'key' => env('S3_KEY', 'key'),
                'secret' => env('S3_SECRET', 'secret'),
                'region' => env('S3_REGION', 'region'),
                'endpoint' => env('S3_END_POINT', 'url'),
                'bucket' => env('S3_BUCKET', 'bucket')
            ],

            'dropbox' => [
                'key' => env('DROPBOX_KEY', 'key')
            ]
        ]
    ]
];
