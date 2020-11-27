<?php

namespace Sculptor\Agent\Webhooks\Controllers;

use Exception;
use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Exceptions\DomainNotFound;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Webhooks\Providers\Factory;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DeployDomainWebhookController extends Controller
{
    public const DONE = 'done';

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var Domains
     */
    private $actions;

    public function __construct(DomainRepository $domains, Domains $actions)
    {
        $this->domains = $domains;

        $this->actions = $actions;
    }

    /**
     * @param Request $request
     * @param string $hash
     * @param string $token
     * @return Response
     * @throws DomainNotFound
     * @throws Exception
     */
    public function deploy(Request $request, string $hash, string $token): Response
    {
        $domain = $this->domains->byHash($hash);

        $done = new Response(DeployDomainWebhookController::DONE, 200, ['Content-Type' => 'text/plain']);

        if (!Throttle::check($request)) {
            abort(429, 'TOO MANY ATTEMPTS');
        }

        if ($domain->token != $token) {
            Logs::batch()->warning("Web hook invalid token");

            Throttle::hit($request, THROTTLE_COUNT, THROTTLE_TIME_SPAN);

            abort(400, 'Invalid token');
        }

        $provider = Factory::deploy($domain->provider);

        Logs::batch()->info("Webhook deploy {$domain->name} branch {$domain->branch} from {$provider->name()} received");

        if (!$provider->valid($request, $domain->branch)) {
            Logs::batch()->error("Webhook deploy {$domain->name} error, payload invalid for {$provider->name()}");

            abort(400, $provider->error() ?? 'Undefined');
        }

        if (!$provider->branch($request, $domain->branch)) {
            Logs::batch()->notice("Web hook {$domain->name} was not for {$domain->branch} branch");

            return $done;
        }

        Logs::batch()->info("Webhook deploy {$domain->name} branch {$domain->branch} from {$provider->name()} deploy append");

        if (!$this->actions->deployBatch($domain->name)) {
            abort(500, $this->actions->error() ?? 'Undefined');
        }

        return $done;
    }
}
