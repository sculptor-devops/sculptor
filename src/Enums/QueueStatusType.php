<?php

namespace Sculptor\Agent\Enums;

class QueueStatusType
{
    const WAITING = 'waiting';

    const RUNNING = 'running';

    const ERROR = 'error';

    const OK = 'ok';

    const FINISHED_STATUSES =  [ QueueStatusType::ERROR, QueueStatusType::OK ];
}
