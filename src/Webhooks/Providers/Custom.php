<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\DeployProviderType;

class Custom implements DeployProvider
{
    public function name(): string
    {
        return DeployProviderType::CUSTOM;
    }

    public function valid(): bool
    {
        return true;
    }

    public function error(): ?string
    {
        return null;
    }
}
