<?php

namespace Sculptor\Agent;


use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    private $address;

    private $token;

    public function __construct(string $address)
    {
        $this->address = $address;
    }

    private function url(string $url=null): string
    {
        return "{$this->address}/api/v1/{$url}";
    }

    private function http(): PendingRequest
    {
        if ($this->token == null) {
            return Http::withoutVerifying()->acceptJson();
        }

        return Http::withoutVerifying()->acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->token}"
            ]);
    }

    public function token(string $token): ApiClient
    {
        $this->token = $token;

        return $this;
    }

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

    public function get(string $name): ?array
    {
        $response = $this->http()
            ->get($this->url($name));

        if ($response->status() == 200) {
            return $response->json();
        }

        return null;
    }

    public function post(string $name, array $data = []): ?array
    {
        $response = $this->http()
            ->post($this->url($name), $data);

        if ($response->status() == 200) {
            return $response->json();
        }

        return null;
    }

    public function logged(): bool
    {
        return $response = $this->http()
                ->get($this->url())->status() == 200;
    }
}
