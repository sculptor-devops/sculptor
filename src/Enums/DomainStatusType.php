<?php

namespace Sculptor\Agent\Enums;

class DomainStatusType
{
    public const NEW = 'new';

    public const CONFIGURED = 'configured';

    public const SETUP = 'setup';

    public const DEPLOYING = 'deploying';

    public const ERROR = 'error';

    public const DEPLOYED = 'deployed';
}
