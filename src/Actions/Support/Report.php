<?php

namespace Sculptor\Agent\Actions\Support;

trait Report
{
    /**
     * @var Action|null
     */
    private $action;

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->action->error();
    }
}
