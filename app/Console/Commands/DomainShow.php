<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Domains\StatusMachine;
use Sculptor\Agent\Ip;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainShow extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:show {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available domains';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param DomainRepository $domains
     * @param Ip $ip
     * @param StatusMachine $machine
     * @return int
     * @throws Exception
     */
    public function handle(DomainRepository $domains, Ip $ip, StatusMachine $machine): int
    {
        $domain = $this->argument('domain');

        if ($domain) {
            $this->show($domain, $domains, $ip, $machine);

            return 0;
        }

        $this->all($domains);

        return 0;
    }

    /**
     * @param string $domain
     * @param DomainRepository $domains
     * @param Ip $ip
     * @param StatusMachine $machine
     * @throws Exception
     */
    private function show(string $domain, DomainRepository $domains, Ip $ip, StatusMachine $machine): void
    {
        $item = $domains->byName($domain);

        $database = 'none';

        $databaseUser = 'none';

        if ($item->database) {
            $database = $item->database->name;
        }

        if ($item->databaseUser) {
            $databaseUser = $item->databaseUser->name;
        }

        $this->table([
            'Name',
            'Value',
        ], [
            ['name' => 'name', 'value' => $item->name],
            ['name' => 'alias', 'value' => $item->alias ?? 'none'],
            ['name' => 'type', 'value' => $item->type],
            ['name' => 'status', 'value' => $item->status . ' (can switch to ' . $machine->next($item->status) . ')'],
            ['name' => 'certificate type', 'value' => $item->certificate],
            ['name' => 'certificate email', 'value' => $item->email ?? 'none'],
            ['name' => 'www', 'value' => $this->yesNo($item->www)],
            ['name' => 'http user', 'value' => $item->user],
            ['name' => 'php engine', 'value' => $item->engine],            
            ['name' => 'root', 'value' => $item->root()],
            ['name' => 'current', 'value' => $item->current()],
            ['name' => 'public', 'value' => $item->home()],
            ['name' => 'database', 'value' => $database . ' (user ' . ( $databaseUser ?? 'none' ) . ')'],
            ['name' => 'deploy command', 'value' => $item->deployer],
            ['name' => 'install command', 'value' => $item->install],
            ['name' => 'git uri', 'value' => $item->vcs],
            ['name' => 'deploy url', 'value' => $item->deployUrl() ?? 'No token configured' ],
            ['name' => 'deploy provider', 'value' => $item->provider ],
            ['name' => 'deploy branch', 'value' => $item->branch ?? 'None' ],
        ]);
    }

    /**
     * @param DomainRepository $domains
     */
    private function all(DomainRepository $domains): void
    {
        $all = $domains->all();

        $tabled = $all->map(function ($domain) {
            return [
                'name' => $domain->name,
                'alias' => $domain->alias ?? 'none',
                'type' => $domain->type,
                'status' => $domain->status,
                'database' => $this->toName($domain->database()),
                'user' => $this->toName($domain->databaseUser()),
                'home' => $domain->home()
            ];
        });

        $this->table([
            'Name',
            'DomainAlias',
            'Type',
            'Status',
            'Database',
            'Alarm',
            'Home'
        ], $tabled);
    }
}
