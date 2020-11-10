<?php

namespace Sculptor\Agent\Webhooks\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
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
     * @return string
     * @throws DomainNotFound
     * @throws Exception
     */
    public function deploy(Request $request, string $hash, string $token)
    {
        $domain = $this->domains->byHash($hash);

        if ($domain->token != $token) {
            Logs::batch()->warning("Web hook invalid token");

            abort(400, 'Invalid token');
        }

        $provider = Factory::deploy($domain->provider);

        Logs::batch()->info("Webhook deploy {$domain->name} branch {$domain->branch} from {$provider->name()} received");

        if (!$provider->valid($request, $domain->branch)) {
            Logs::batch()->error("Webhook deploy {$domain->name} error, payload invalid for {$provider->name()}");

            abort(400, $provider->error());
        }

        if (!$provider->branch($request, $domain->branch)) {
            Logs::batch()->notice("Web hook {$domain->name} was not for {$domain->branch} branch");

            return DeployDomainWebhookController::DONE;
        }

        Logs::batch()->info("Webhook deploy {$domain->name} branch {$domain->branch} from {$provider->name()} deploy append");

        if (!$this->actions->deployBatch($domain->name)) {
            abort(500, $this->actions->error());
        }

        return DeployDomainWebhookController::DONE;
    }
}
