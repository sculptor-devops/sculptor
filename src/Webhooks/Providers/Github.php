<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Exception;
use Illuminate\Http\Request;
use Sculptor\Agent\Contracts\DeployProvider;
use Sculptor\Agent\Enums\VersionControlType;
use Sculptor\Agent\Facades\Logs;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Github implements DeployProvider
{
    /**
     * @var string|null
     */
    private $error;

    /**
     * @return string
     */
    public function name(): string
    {
        return VersionControlType::GITHUB;
    }

    /**
     * @param Request $request
     * @param string $branch
     * @return bool
     */
    public function branch(Request $request, string $branch): bool
    {
        try {
            if (!$request->isJson()) {
                throw new Exception("Is not a valid json");
            }

            $payload = $request->json();

            if ($payload->get('ref') != "refs/heads/{$branch}") {
                return false;
            }

            return true;
        } catch (Exception $e) {
            Logs::batch()->report($e);

            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @param Request $request
     * @param string $branch
     * @return bool
     */
    public function valid(Request $request, string $branch): bool
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }
}
