<?php

namespace App\Console\Commands;


use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Support\CommandBase;

class DatabaseShow extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show databases';

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
     * @param DatabaseRepository $databases
     * @return int
     */
    public function handle(DatabaseRepository $databases): int
    {
        $tabled = [];

        $all = $databases->all();

        foreach ($all as $item) {
            $tabled[] = [
                'name' => $item->name,
                'type' => $item->driver,
                'users' => $this->toName($item->users()),
                'domains' => $this->toName($item->domains())
            ];
        }

        $this->table(['Name', 'Type', 'Users', 'Domains'], $tabled);

        return 0;
    }

    private function toName(?Relation $collection): string
    {
        if ($collection == null) {
            return 'none';
        }

        return $collection->get(['name'])
            ->map(function ($user) {
                return $user->name;
            })
            ->join(', ');
    }
}
