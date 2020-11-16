<?php

namespace Sculptor\Agent\Monitors\Actions;

use Exception;
use Illuminate\Support\Facades\Http;
use Sculptor\Agent\Contracts\AlarmAction;
use Sculptor\Agent\Monitors\Context;
use Sculptor\Agent\Monitors\Parametrizer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Webhook implements AlarmAction
{
    /**
     * @param string $to
     * @param string $message
     * @param array $context
     * @throws Exception
     */
    public function emit(string $to, string $message, array $context): void
    {
        $parameters = new Parametrizer($to);

        $replaced = new Context($message);

        $context = $replaced->parse($context);

        switch ($parameters->first()) {
            case 'post':
                Http::withoutVerifying()
                    ->post($parameters->last(), $context);

                break;

            case 'get':
                Http::withoutVerifying()
                    ->get($parameters->last(), $context);

                break;

            default:
                throw new Exception("Invalid http verb {$parameters->first()}");
        }
    }
}
