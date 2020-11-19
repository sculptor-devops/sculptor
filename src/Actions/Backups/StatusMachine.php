<?php

namespace Sculptor\Agent\Actions\Backups;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupStatusType;
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
        BackupStatusType::NONE => [
            BackupStatusType::OK,
            BackupStatusType::ERROR
        ],

        BackupStatusType::OK => [
            BackupStatusType::RUNNING,
            BackupStatusType::ERROR,
            BackupStatusType::OK
        ],

        BackupStatusType::RUNNING => [
            BackupStatusType::RUNNING,
            BackupStatusType::ERROR,
            BackupStatusType::OK
        ],

        BackupStatusType::ERROR => [ /* ALL */ ]
    ];

    public function __construct(Configuration $configuration)
    {
        parent::__construct(StatusMachine::STATUSES);

        $this->configuration = $configuration;
    }
}
