<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Agent\Support\PhpVersions;
use Sculptor\Agent\Support\Templates;
use Sculptor\Agent\Commands\Wizard;
use Sculptor\Agent\Enums\CertificatesTypes;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainWizard extends CommandBase
{
    use Wizard;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:wizard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a guided domain';

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
     * @param PhpVersions $versions
     * @return int
     */
    public function handle(PhpVersions $versions, Templates $templates): int
    {
        $this->steps = 6;

        try {
            $type = $this->choose("Domain type", $templates->domains(), function ($item) {
                return __($item->name());
            });

            $certificate = $this->choose("Certificate", [
                CertificatesTypes::SELF_SIGNED => 'Self signed',
                CertificatesTypes::CUSTOM => 'Custom certificate',
                CertificatesTypes::LETS_ENCRYPT => 'Let\'s Encrypt'
            ]);

            if ($certificate == CertificatesTypes::LETS_ENCRYPT) {
                $email = $this->input("Certificate owner", 'Insert email...', '', false, 'required|email');
            }

            $php = $this->choose("PHP version", $versions->available());

            $name = $this->input("Domain name", 'Insert name...', '', false, 'required|fqdn');

            $database = $this->input("Database", 'Name...', 'database_name', true, 'required|alpha_num');

            $repository = $this->input("Repository", 'Insert url...', 'https://<<token>>@ghtub.com/username/repository.git', false, 'required|vcs');

            $user = "{$database}_user";

            $this->table(['Name', 'Value'], [
                ['name' => 'Template', 'value' => $type],
                ['name' => 'PHP', 'value' => $php],
                ['name' => 'Domain', 'value' => $name],
                ['name' => 'Repository', 'value' => $repository],
                ['name' => 'Certificate', 'value' => $certificate],
                ['name' => 'Certificate email', 'value' => $email ?? '<NONE>'],
                ['name' => 'Database', 'value' => !$database ? '<NONE>' : $database],
                ['name' => 'Database user', 'value' => !$database ? '<NONE>' : $user],
            ]);

            if (!$this->askYesNo('Continue? (yes/no)')) {
                throw new Exception('Command stopped');
            }

            foreach ([
                'domain' => ['command' => 'domain:create', 'parameters' => ['name' => $name, 'type' => $type]],
                'database' => ['command' => 'database:create', 'parameters' => ['name' => $database]],
                'database user' => ['command' => 'database:user', 'parameters' => ['name' => $user, 'database' => $database]],

                'php' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'engine', 'value' => $php]],
                'Certificate' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'certificate', 'value' => $certificate]],
                'Certificate user' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'email', 'value' => $email]],
                'repository' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'vcs', 'value' => $repository]],
                'database assign' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'database', 'value' => $database]],
                'database user assign' => ['command' => 'domain:setup', 'parameters' => ['name' => $name, 'parameter' => 'user', 'value' => $user]],

                'configuration' => ['command' => 'domain:configure', 'parameters' => ['name' => $name]]
            ] as $name => $data) {

                if (!$data['parameters']['value']) {
                    $this->warn("Step {$name} skipped...");

                    continue;
                }

                if (!$this->command($name, $data['command'], $data['parameters'])) {
                    throw new Exception(Artisan::output());
                }
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->warn("Now you can domain:deploy {$name} to apply modifications, or continue to customize configuations");

        return 0;
    }
}
