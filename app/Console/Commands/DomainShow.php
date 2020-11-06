<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Ip;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Support\CommandBase;

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
     * @return int
     * @throws Exception
     */
    public function handle(DomainRepository $domains, Ip $ip): int
    {
        $domain = $this->argument('domain');

        if ($domain) {
            $this->show($domain, $domains, $ip);

            return 0;
        }

        $this->all($domains);

        return 0;
    }

    /**
     * @param string $domain
     * @param DomainRepository $domains
     * @param Ip $ip
     * @throws Exception
     */
    private function show(string $domain, DomainRepository $domains, Ip $ip): void
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
            ['name' => 'status', 'value' => $item->status],
            ['name' => 'certificate type', 'value' => $item->certificate],
            ['name' => 'www', 'value' => $this->yesNo($item->www)],
            ['name' => 'user', 'value' => $item->user],
            ['name' => 'root', 'value' => $item->root()],
            ['name' => 'http user', 'value' => $item->user],
            ['name' => 'home', 'value' => $item->home()],
            ['name' => 'database', 'value' => $database . ' (user ' . ( $databaseUser ?? 'none' ) . ')',
            ['name' => 'deploy command', 'value' => $item->deployer],
            ['name' => 'install command', 'value' => $item->install],
            ['name' => 'git uri', 'value' => $item->vcs],
            ['name' => 'deploy url', 'value' => $item->deployUrl() ?? 'No token configured' ],
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
            'Alias',
            'Type',
            'Status',
            'Database',
            'User',
            'Home'
        ], $tabled);
    }
}
