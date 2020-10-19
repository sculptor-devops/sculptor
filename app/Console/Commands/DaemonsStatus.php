<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Daemons;
use Sculptor\Agent\Support\CommandBase;

class DaemonsStatus extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemons:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daemons status';

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
     * @param Daemons $actions
     * @return int
     */
    public function handle(Daemons $actions): int
    {
        $tabled = collect($actions->status())
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'group' => Str::upper($item['group']),
                    'active' => $item['active'] ? '<info>YES</info>' : '<error>NO</error>'
                ];
            });

        $this->table(['Service', 'Group', 'Active'], $tabled);

        return 0;
    }
}
