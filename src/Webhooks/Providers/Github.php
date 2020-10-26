<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\DeployProviderType;

class Github implements DeployProvider
{
    public function name(): string
    {
        return DeployProviderType::GITHUB;
    }

    public function valid(): bool
    {
        return true;
    }

    public function error(): ?string
    {
        return 'none';
    }
}
