<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Exception;
use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\VersionControlType;

class Factory
{
    /**
     * @param string $type
     * @return DeployProvider
     * @throws Exception
     */
    public static function deploy(string $type): DeployProvider
    {
        switch ($type) {
            case VersionControlType::GITHUB:
                return new Github();

            case VersionControlType::CUSTOM:
                return new Custom();
        }

        throw new Exception("Invalid vcs provider {$type}");
    }
}
