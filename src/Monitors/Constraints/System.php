<?php

namespace Sculptor\Agent\Monitors\Constraints;

use Exception;
use Sculptor\Agent\Contracts\Constraint;
use Sculptor\Agent\Monitors\Parametrizer;
use Sculptor\Agent\Monitors\System as Monitors;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class System implements Constraint
{
    /**
     * @var Monitors
     */
    private $monitors;

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var bool
     */
    private $act = false;

    /**
     * System constructor.
     * @param Monitors $monitors
     */
    public function __construct(Monitors $monitors)
    {
        $this->monitors = $monitors;
    }

    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param string $threshold
     * @return bool
     * @throws Exception
     */
    public function threshold(bool $alarmed, string $rearm, string $threshold): bool
    {
        $parameters = new Parametrizer($threshold);

        $monitor = $parameters->first();

        $limit = $parameters->last();

        $data = collect($this->monitors->last());

        $values = $data->filter(function ($item, $key) use ($monitor) {
            return $key == $monitor;
        });

        if ($values->count() != 1) {
            return false;
        }

        $value = floatval($values->get($monitor));

        $this->context = [
            'limit' => $limit,
            'value' => $value,
            'alarmed' => $alarmed,
            'rearm' => $rearm,
            'monitor' => $monitor
        ];

        return $this->evaluate($alarmed, $rearm, $value, $limit);
    }

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
