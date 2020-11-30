<?php

namespace Sculptor\Agent\Monitors\Support;

trait Condition
{
    /**
     * @var array
     */
    private $context = [];

    /**
     * @var bool
     */
    private $act = false;
    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param float $value
     * @param float $limit
     * @return bool
     */
    private function evaluate(bool $alarmed, string $rearm, float $value, float $limit): bool
    {
        switch ($rearm) {
            case 'auto':
                $this->act = ($value > $limit);

                break;

            case 'manual':
                $this->act = ($value > $limit) && !$alarmed;

                break;
        }

        return ($value > $limit);
    }

    /**
     * @return bool
     */
    public function act(): bool
    {
        return $this->act;
    }

    /**
     * @return array
     */
    public function context(): array
    {
        return $this->context;
    }
}
