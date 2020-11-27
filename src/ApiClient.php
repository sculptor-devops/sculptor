<?php

namespace Sculptor\Agent;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ApiClient
{
    /**
     * @var string
     */
    private $address;

    /**
     * @var string|null
     */
    private $token;

    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * @param string|null $url
     * @return string
     */
    private function url(string $url = null): string
    {
        return "{$this->address}/api/v1/{$url}";
    }

    /**
     * @return PendingRequest
     */
    private function http(): PendingRequest
    {
        if ($this->token == null) {
            return Http::withoutVerifying()->acceptJson();
        }

        return Http::withoutVerifying()
            ->acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->token}"
            ]);
    }

    /**
     * @param string $token
     * @return $this
     */
    public function token(string $token): ApiClient
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login(string $username, string $password): bool
    {
        $response = $this->http()
            ->post($this->url('login'), [
                'email' => $username,
                'password' => $password
            ]);

        if ($response->status() == 200) {
            $this->token = $response->json()['access_token'];

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array
    {
        $response = $this->http()
            ->get($this->url($name));

        if ($response->status() == 200) {
            return $response->json();
        }

        return null;
    }

    /**
     * @param string $name
     * @param array $data
     * @return array|null
     */
    public function post(string $name, array $data = []): ?array
    {
        $response = $this->http()
            ->post($this->url($name), $data);

        if ($response->status() == 200) {
            return $response->json();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function logged(): bool
    {
        return $this->http()
                ->get($this->url())
                ->status() == 200;
    }
}
