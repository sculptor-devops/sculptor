<?php

namespace Sculptor\Agent\Actions\Domains;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Support\StateMachine;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class StatusMachine extends StateMachine
{
    /**
     * @var Configuration
     */
    private $configuration;

    public const STATUSES = [
        DomainStatusType::NEW => [
            DomainStatusType::SETUP,
            DomainStatusType::CONFIGURED,
            DomainStatusType::NEW,
            DomainStatusType::ERROR
        ],

        DomainStatusType::SETUP => [
            DomainStatusType::CONFIGURED,
            DomainStatusType::SETUP,
            DomainStatusType::ERROR
        ],

        DomainStatusType::CONFIGURED => [
            DomainStatusType::DEPLOYED,
            DomainStatusType::DEPLOYING,
            DomainStatusType::SETUP,
            DomainStatusType::CONFIGURED,
            DomainStatusType::ERROR
        ],

        DomainStatusType::DEPLOYED => [
            DomainStatusType::DEPLOYED,
            DomainStatusType::DEPLOYING,
            DomainStatusType::CONFIGURED,
            DomainStatusType::SETUP,
            DomainStatusType::ERROR
        ],

        DomainStatusType::DEPLOYING => [
            DomainStatusType::DEPLOYED,
            DomainStatusType::ERROR
        ],

        DomainStatusType::ERROR => [ /* ALL */ ]
    ];

    public function __construct(Configuration $configuration)
    {
        parent::__construct(StatusMachine::STATUSES);

        $this->configuration = $configuration;
    }
}
