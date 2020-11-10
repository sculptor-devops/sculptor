<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Exception;
use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\VersionControlType;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
