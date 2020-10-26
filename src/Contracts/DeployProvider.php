<?php

namespace Sculptor\Agent\Contracts;

interface DeployProvider
{
    public function name(): string;

    public function valid(): bool;

    public function error(): ?string;
}
