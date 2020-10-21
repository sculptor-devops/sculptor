<?php

namespace Sculptor\Agent;

use Sculptor\Foundation\Contracts\Runner;

class PasswordGenerator
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function create(int $length = null): string
    {
        if ($length == null) {
            $min = config('sculptor.security.password.min');

            $max = config('sculptor.security.password.max');

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
