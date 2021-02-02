<?php

namespace App\Console\Commands;

use _HumbugBox1aa671719151\Nette\Neon\Exception;
use Illuminate\Support\Facades\Http;
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
        $updates = $this->updates();

        $current = composerVersion();

        $this->table([
            'Name',
            'Value'
        ],
            [
                ['Name', 'Sculptor Devops'],
                ['Version', $current],
                ['URL', env('APP_URL')],
                ['PHP', $configurations->get('sculptor.php.version')],
                ['DB', $configurations->get('sculptor.database.default')],
                ['Operating system', "{$version->name()}"],
                ['Architecture', "{$version->arch()} ({$version->bits()} bit)"],
            ]);

        $this->info("Modules: " . Str::upper(env('SCULPTOR_INSTALLED_MODULES')));

        if ($updates != $current) {
            $this->warn("New update available {$updates}");
        }

        return 0;
    }

    private function updates(): ?string
    {
        try {
            $updates = Http::get('https://repo.packagist.org/p/sculptor-devops/sculptor.json');

            if (!$updates->status() == 200) {
                throw new Exception("Http error returned status {$updates->status()}");
            }

            $version = collect($updates->json('packages')['sculptor-devops/sculptor'])->keys()->sort()->last();

            if (Str::startsWith($version, 'v')) {
                return Str::replaceFirst('v', '', $version);
            }
        } catch (\Exception $e) {
            $this->warn("Cannot contact packagist: {$e->getMessage()}");
        }

        return null;
    }
}
