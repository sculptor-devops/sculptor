<?php

namespace Sculptor\Agent\Monitors\Conditions;

use Exception;
use Illuminate\Support\Facades\Http;
use Sculptor\Agent\Contracts\AlarmCondition;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Monitors\Parametrizer;
use Sculptor\Agent\Monitors\Support\Condition;
use Sculptor\Agent\Monitors\System as Monitors;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ResponseTime implements AlarmCondition
{
    use Condition;

    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param string $threshold
     * @return bool
     * @throws Exception
     */
    public function threshold(bool $alarmed, string $rearm, string $threshold): bool
    {
        $now = now();

        $parameters = new Parametrizer($threshold);

        $url = $parameters->first();

        $limit = $parameters->last();

        $response = Http::withoutVerifying()->get($url);

        $value = now()->diffInMilliseconds($now);

        $this->context = [
            'limit' => $limit,
            'value' => $value,
            'alarmed' => $alarmed,
            'rearm' => $rearm,
            'url' => $url
        ];

        $evaluation = $this->evaluate($alarmed, $rearm, $value, $limit) && $response->successful();

        if ($evaluation && $this->act) {
            Logs::batch()->notice("Response time {$url} is {$value} > {$limit}");
        }

        return $evaluation;
    }
}
