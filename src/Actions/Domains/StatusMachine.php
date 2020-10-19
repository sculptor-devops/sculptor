<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Exceptions\DomainStatusException;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

class StatusMachine
{
    /**
     * @param string $from
     * @param string $status
     * @return bool
     * @throws Exception
     */
    public function can(string $from, string $status): bool
    {
        if ($status == DomainStatusType::NEW) {
            throw new DomainStatusException($status, $from);
        }

        if ($status == DomainStatusType::CONFIGURED && in_array($from,
                [
                    DomainStatusType::NEW,
                    DomainStatusType::DEPLOYED,
                    DomainStatusType::SETUP,
                    DomainStatusType::CONFIGURED
                ])) {
            throw new DomainStatusException($status, $from);
        }

        if ($status == DomainStatusType::DEPLOYED && in_array($from, [
                DomainStatusType::CONFIGURED,
                DomainStatusType::DEPLOYED
            ])) {
            throw new DomainStatusException($status, $from);
        }

        if ($status == DomainStatusType::SETUP && in_array($from, [
                DomainStatusType::CONFIGURED,
                DomainStatusType::SETUP
            ])) {
            throw new DomainStatusException($status, $from);
        }

        return true;
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
