<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Exceptions\DomainStatusException;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class StatusMachine
{
    /**
     * @var Configuration
     */
    private $configuration;

    private $from = [
        DomainStatusType::DEPLOYING => [],

        DomainStatusType::ERROR => [],

        DomainStatusType::NEW => [
            DomainStatusType::NEW,
            DomainStatusType::CONFIGURED
        ],

        DomainStatusType::CONFIGURED => [
            DomainStatusType::NEW,
            DomainStatusType::DEPLOYED,
            DomainStatusType::DEPLOYING,
            DomainStatusType::SETUP,
            DomainStatusType::CONFIGURED
        ],

        DomainStatusType::DEPLOYED => [
            DomainStatusType::CONFIGURED,
            DomainStatusType::DEPLOYED,
            DomainStatusType::DEPLOYING
        ],

        DomainStatusType::SETUP => [
            DomainStatusType::DEPLOYED,
            DomainStatusType::DEPLOYING,
            DomainStatusType::CONFIGURED,
            DomainStatusType::SETUP,
            DomainStatusType::NEW
        ]
    ];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     * @throws Exception
     */
    public function can(string $from, string $to): bool
    {
        $available = $this->from[$to];

        if (in_array($from, $available) || count($available) == 0 || $available == null) {
            return true;
        }

        throw new DomainStatusException($from, $to);
    }

    public function next(string $status): string
    {
        $statuses = $this->from[$status];

        if (count($statuses) == 0) {
            return "Can go in any status";
        }

        return implode(', ', $statuses);
    }

    /**
     * @param Domain $domain
     * @param string $status
     * @return bool
     * @throws Exception
     */
    public function change(Domain $domain, string $status): bool
    {
        if ($this->can($domain->status, $status)) {
            $domain->update(['status' => $status]);

            Logs::actions()->notice("Domain {$domain->name} status changed from {$domain->status} to {$status}");

            return true;
        }

        return false;
    }
}
