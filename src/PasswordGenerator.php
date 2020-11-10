<?php

namespace Sculptor\Agent;

use Illuminate\Support\Str;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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

    public function token(int $length = 32): string
    {
        $token = $this->create($length * 2);

        $token = preg_replace('/[^A-Za-z0-9 ]/', '', $token);

        return Str::limit($token, $length, '');
    }
}
