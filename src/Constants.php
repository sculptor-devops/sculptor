<?php
define('COMMAND_INTERFACE', 1);
define('BLUEPRINT_VERSION', 1);
define('API_VERSION', 1);
define('SITES_HOME', '/home');
define('SITES_USER', 'www');
define('SITES_PUBLIC', 'public');
define('SITES_DEPLOY', 'deploy');
define('SITES_INSTALL', 'deploy:install');

define('BACKUP_CRON', '0 0 * * *');
define('BACKUP_ROTATE', 7);

define('SCULPTOR_HOME', '/home/sculptor');
define('DB_SERVER_PASSWORD', '/home/sculptor/.db_password');
define('QUEUE_TASK_MILLISECOND', 1000);
define('QUEUE_TASK_ROUND_TRIP', 250 * QUEUE_TASK_MILLISECOND);
define('QUEUE_TASK_TIMEOUT', 10000 * QUEUE_TASK_MILLISECOND);
define('QUEUE_TASK_NO_TIMEOUT', 0);
