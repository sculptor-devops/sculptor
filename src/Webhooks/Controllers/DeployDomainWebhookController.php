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

class DeployDomainWebhookController extends Controller
{
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
            abort(400, 'Invalid token');
        }

        $provider = Factory::deploy($domain->provider);

        Logs::job()->info("Webhook deploy {$domain->name} branch {$domain->branch} from {$provider->name()} received");

        if ($provider->valid($request)) {
            abort(400, $provider->error());
        }

        if (!$this->actions->deploy($domain)) {
            abort(500, $this->actions->error());
        }

        return 'done';
    }
}