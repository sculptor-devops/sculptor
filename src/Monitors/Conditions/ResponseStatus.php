<?php

namespace Sculptor\Agent\Monitors\Conditions;

use Exception;
use Illuminate\Support\Facades\Http;
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

class ResponseStatus implements AlarmCondition
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
        $parameters = new Parametrizer($threshold);

        $url = $parameters->first();

        $code = $parameters->last();

        $response = Http::withoutVerifying()->get($url);

        $value = $response->successful() && ($response->status() == $code);

        $this->context = [
            'code' => $code,
            'value' => $value,
            'alarmed' => $alarmed,
            'rearm' => $rearm,
            'url' => $url
        ];

        $evaluation = $this->evaluate($alarmed, $rearm, $value, $code);

        if ($evaluation && $this->act) {
            Logs::batch()->notice("Response time {$url} is {$value} is not {$code}");
        }

        return $evaluation;
    }
    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param int $value
     * @param int $code
     * @return bool
     */
    private function evaluate(bool $alarmed, string $rearm, int $value, int $code): bool
    {
        switch ($rearm) {
            case 'auto':
                $this->act = ($value == $code);

                break;

            case 'manual':
                $this->act = ($value == $code) && !$alarmed;

                break;
        }

        return ($value == $code);
    }
}
