<?php

namespace Sculptor\Agent\Contracts;

use Illuminate\Http\Request;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface DeployProvider
{
    public function name(): string;

    public function valid(Request $request, string $branch): bool;

    public function branch(Request $request, string $branch): bool;

    public function error(): ?string;
}
