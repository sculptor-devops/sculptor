<?php

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

define('THROTTLE_COUNT', 20);
define('THROTTLE_TIME_SPAN', 360);
define('SITES_HOME', '/home');
define('SITES_USER', 'www');
define('SITES_PUBLIC', 'public');
define('SITES_DEPLOY', 'deploy');
define('SITES_INSTALL', 'deploy:install');
define('PHP_AGENT_VERSION', '8.0');
define('ENGINE_VERSION', '8.0');
define('ENGINE_PATH', '/usr/bin/php');
define('BLUEPRINT_VERSION', 1);

define('BACKUP_CRON', '0 0 * * *');
define('BACKUP_ROTATE', 7);

define('SCULPTOR_HOME', '/home/sculptor');
define('DB_SERVER_PASSWORD', '/home/sculptor/.db_password');
define('QUEUE_TASK_MILLISECOND', 1000);
define('QUEUE_TASK_ROUND_TRIP', 250 * QUEUE_TASK_MILLISECOND);
define('QUEUE_TASK_TIMEOUT', 10000 * QUEUE_TASK_MILLISECOND);
define('QUEUE_TASK_NO_TIMEOUT', 0);
