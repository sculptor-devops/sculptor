<?php


namespace App\Console\Commands;


use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Repositories\DomainRepository;

class DomainList extends Command
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
     * @return int
     * @throws Exception
     */
    public function handle(DomainRepository $domains): int
    {
        $domain = $this->argument('domain');

        if ($domain) {
            $this->info($domain);

            return 0;
        }

        $this->all($domains);

        return 0;
    }

    private function info(string $domain): void
    {

    }

    private function all(DomainRepository $domains): void
    {
        $all = $domains->all();

        $this->table([
            'Name',
            'Alias',
            'Type',

        ], $all->flatMap(function ($domain) {
            return [
                'name' => $domain->name,
                'alias' => $domain->alias ?? 'none',
                'type' => $domain->type,
                'certificate' => $domain->certificate,
                'user' => $domain->user,
                'home' => $domain->home,
                'database' => $domain->database ?? 'none',
                'user' => $domain->databaseUser ?? 'none',
            ];
        }));
    }
}
