<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainSecurity extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:security {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domain security';

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
     * @param Domains $domains
     * @return int
     * @throws Exception
     * @throws GuzzleException
     */
    public function handle(Domains $domains): int
    {
        $name = $this->argument('name');

        $this->startTask("Security check {$name}");

        $checks = $domains->security($name);

        $this->completeTask();

        $tabled = (collect($checks)->map(function ($item, $key) {
            return [
                'package' => "{$key}@{$item['version']}",
                'time' => Carbon::parse($item['time'])->format('Y:m:d'),
                'advisories' => collect($item['advisories'])->map(function($item) {
                    return $item['link'];
                })->first(),
            ];
        }));


        if ($tabled->count()) {
            $this->table(['Package', 'Time', 'Description'], $tabled);

            return 0;    
        }


        $this->info('No issue found');

        return 0;
    }
}
