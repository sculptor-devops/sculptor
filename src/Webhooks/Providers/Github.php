<?php

namespace Sculptor\Agent\Webhooks\Providers;


use Exception;
use Illuminate\Http\Request;
use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\VersionControlType;
use Sculptor\Agent\Facades\Logs;

class Github implements DeployProvider
{
    public function name(): string
    {
        return VersionControlType::GITHUB;
    }

    public function valid(Request $request, string $branch): bool
    {
        try {
            if (!$request->isJson()) {
                Logs::batch()->error('The request is not a valid json');

                return false;
            }

            $payload = $request->json();

            if ($payload->get('ref') != "refs/heads/{$branch}") {
                return false;
            }

            return true;
        } catch (Exception $e) {
            Logs::batch()->report($e);

            return false;
        }
    }

    public function error(): ?string
    {
        return 'none';
    }
}
