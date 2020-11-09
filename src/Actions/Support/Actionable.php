<?php

namespace Sculptor\Agent\Actions\Support;

use Sculptor\Agent\Repositories\Entities\Queue;

trait Actionable
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

    public function inserted(): ?Queue
    {
        return $this->action->inserted();
    }
}
