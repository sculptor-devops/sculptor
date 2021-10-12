<?php

namespace Sculptor\Agent\Monitors\Conditions;

use Exception;
use Sculptor\Agent\Contracts\AlarmCondition;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Support\Parametrizer;
use Sculptor\Agent\Monitors\Support\Condition;
use Sculptor\Agent\Monitors\System as Monitors;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class System implements AlarmCondition
{
    use Condition;

    /**
     * @var Monitors
     */
    private $monitors;
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

        $evaluation = $this->evaluate($alarmed, $rearm, $value, $limit);

        if ($evaluation && $this->act) {
            Logs::batch()->notice("System monitor limit on {$monitor} is {$value} > {$limit}");
        }

        return $evaluation;
    }
}
