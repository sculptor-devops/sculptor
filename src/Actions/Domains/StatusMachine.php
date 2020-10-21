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
     * @param string $to
     * @return bool
     * @throws Exception
     */
    public function can(string $from, string $to): bool
    {
        if (!config('sculptor.domains.state-machine')) {
            return true;
        }

        if ($to == DomainStatusType::NEW && in_array($from,
                [
                    DomainStatusType::NEW,
                    DomainStatusType::CONFIGURED
                ])) {
            return true;
        }

        if ($to == DomainStatusType::CONFIGURED && in_array($from,
                [
                    DomainStatusType::NEW,
                    DomainStatusType::DEPLOYED,
                    DomainStatusType::SETUP,
                    DomainStatusType::CONFIGURED
                ])) {
            return true;
        }

        if ($to == DomainStatusType::DEPLOYED && in_array($from,
                [
                    DomainStatusType::CONFIGURED,
                    DomainStatusType::DEPLOYED
                ])) {
            return true;
        }

        if ($to == DomainStatusType::SETUP && in_array($from,
                [
                    DomainStatusType::CONFIGURED,
                    DomainStatusType::SETUP,
                    DomainStatusType::NEW
                ])) {
            return true;
        }

        throw new DomainStatusException($from, $to);
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
