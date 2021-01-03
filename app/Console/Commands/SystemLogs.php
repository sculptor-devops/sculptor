<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Logs\Support\LogIpContext;
use Sculptor\Agent\Logs\Support\LogTagContext;
use Sculptor\Agent\Logs\Parser;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemLogs extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:logs {operation=show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sho system logs';

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
     * @param Parser $logs
     * @return int
     * @throws Exception
     */
    public function handle(Parser $logs): int
    {
        $operation = $this->argument('operation');

        if ($operation == 'show') {
            $this->table(['Available files'], $logs
                ->files()
                ->map(function ($file) {
                    return [$file];
                })->toArray());

            return 0;
        }

        $rows = $logs->file($operation);

        $this->table(['Level', 'Ip', 'Tag', 'Date', 'Message', 'Stack'],
            $rows->map(function ($row) {
                $context = new LogIpContext($row['context']);

                return [
                    'level' => $row['level'],
                    'ip' => new LogIpContext($row['context']),
                    'tag' =>  new LogTagContext($row['context']),
                    'date' => Carbon::parse($row['date']),
                    'text' => Str::limit($row['text'], 50),
                    'stack' => $this->noYes($row['stack'] == null)
                ];
            })
            ->toArray());

        $this->warn("{$rows->count()} lines");

        return 0;
    }
}
