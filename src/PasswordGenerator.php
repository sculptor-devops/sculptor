<?php

namespace Sculptor\Agent;

use Sculptor\Foundation\Contracts\Runner;

class PasswordGenerator
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

    public function create(int $length = null): string
    {
        if ($length == null) {
            $min = $this->configuration->get('sculptor.security.password.min');

            $max = $this->configuration->get('sculptor.security.password.max');

            $length = rand($min, $max);
        }

        $password = $this->runner
            ->runOrFail([
                'openssl',
                'rand',
                '-base64',
                $length
            ]);

        $password = trim(preg_replace('/\s+/', ' ', $password));

        return $password;
    }
}
