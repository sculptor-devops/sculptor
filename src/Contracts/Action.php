<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Repositories\Entities\Queue;

interface Action
{
    public function error(): ?string;

    public function inserted(): ?Queue;
}
