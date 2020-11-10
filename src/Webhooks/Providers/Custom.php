<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Illuminate\Http\Request;
use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\VersionControlType;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Custom implements DeployProvider
{
    public function name(): string
    {
        return VersionControlType::CUSTOM;
    }

    public function valid(Request $request, string $branch): bool
    {
        return true;
    }

    public function branch(Request $request, string $branch): bool
    {
        return true;
    }

    public function error(): ?string
    {
        return null;
    }
}
