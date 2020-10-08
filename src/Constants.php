<?php
define('DB_SERVER_PASSWORD', '/home/sculptor/.db_password');
define('QUEUE_TASK_ROUND_TRIP', 250000);
define('QUEUE_TASK_TIMEOUT', 10000000);
define('QUEUE_STATUS_WAITING', 'waiting');
define('QUEUE_STATUS_RUNNING', 'running');
define('QUEUE_STATUS_ERROR', 'error');
define('QUEUE_STATUS_OK', 'ok');

define('QUEUE_STATUSES', [
    QUEUE_STATUS_WAITING,
    QUEUE_STATUS_RUNNING,
    QUEUE_STATUS_ERROR,
    QUEUE_STATUS_OK
]);

define('QUEUE_FINISHED_STATUSES', [
    QUEUE_STATUS_ERROR,
    QUEUE_STATUS_OK
]);

