<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Foundation\Support\Version;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemInfo extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System informations';

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
     * @param Configuration $configurations
     * @param Version $version
     * @return int
     */
    public function handle(Configuration $configurations, Version $version): int
    {
        $this->table([
            'Name',
            'Value'
        ],
        [
            [ 'Name', 'Sculptor Devops'],
            [ 'URL', env('APP_URL')],
            [ 'PHP', $configurations->get('sculptor.php.version')],
            [ 'DB', $configurations->get('sculptor.database.default')],
            [ 'Command version', COMMAND_INTERFACE],
            [ 'API version', API_VERSION],
            [ 'Blueprint version', BLUEPRINT_VERSION],
            [ 'Operating system', "{$version->name()}"],
            [ 'Architecture', "{$version->arch()} {$version->bits()} bit"],
        ]);

        $this->info("Modules: " . Str::upper(env('SCULPTOR_INSTALLED_MODULES')));

        return 0;
    }
}
