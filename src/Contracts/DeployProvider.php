<?php

namespace Sculptor\Agent\Contracts;

use Illuminate\Http\Request;

interface DeployProvider
{
    public function name(): string;

    public function valid(Request $request): bool;

    public function error(): ?string;
}
