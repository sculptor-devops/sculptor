<?php

namespace Sculptor\Agent\Enums;

class QueueStatusType
{
    public const WAITING = 'waiting';

    public const RUNNING = 'running';

    public const ERROR = 'error';

    public const OK = 'ok';

    public const FINISHED_STATUSES =  [ QueueStatusType::ERROR, QueueStatusType::OK ];
}
