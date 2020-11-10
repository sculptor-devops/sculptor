<?php

namespace Sculptor\Agent;

use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Ip
{
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Runner $runner, Configuration $configuration)
    {
        $this->runner = $runner;

        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function publicIp(): string
    {
        $ip = quoteContent($this->runner
            ->runOrFail([
                'dig',
                '-4',
                'TXT',
                '+short',
                'o-o.myaddr.l.google.com',
                '@ns1.google.com'
            ]));

        if ($ip == null || $ip == '') {
            $ip = quoteContent($this->runner
                ->runOrFail([
                    'dig',
                    '-6',
                    'TXT',
                    '+short',
                    'o-o.myaddr.l.google.com',
                    '@ns1.google.com'
                ]));
        }

        return $ip;
    }
}
