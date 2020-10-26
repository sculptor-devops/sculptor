<?php

namespace Sculptor\Agent;

use Sculptor\Foundation\Contracts\Runner;

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
