<?php

namespace Sculptor\Agent\Contracts;

interface BlueprintRecord
{
    public function serialize(): array;
    public function serializeFiler(): array;
}
