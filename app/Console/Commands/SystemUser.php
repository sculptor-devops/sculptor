<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Users;
use Sculptor\Agent\ApiClient;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemUser extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:user {operation=show} {email?} {option1?} {option2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage system users';

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
     * @param Users $users
     * @param PasswordGenerator $passwords
     * @return int
     */
    public function handle(Users $users, PasswordGenerator $passwords): int
    {
        $operation = $this->argument('operation');

        $email = $this->argument('email');

        $option1 = $this->argument('option1');

        $option2 = $this->argument('option2');

        $this->startTask("System users {$operation}");

        switch ($operation) {
            case 'create':
                if ($option2 == null) {
                    $option2 = $passwords->create();
                }

                if (!$users->create($option1 ?? $email, $email, $option2)) {
                    $this->error($users->error());
                }

                $this->completeTask();

                return 0;

            case 'password':
                if ($option1 == null) {
                    $option1 = $passwords->create();
                }

                if (!$users->password($email, $option1)) {
                    $this->error($users->error());
                }

                $this->completeTask();

                $this->warn("Password is {$option1}");

                return 0;

            case 'delete':
                if (!$users->delete($email)) {
                    $this->error($users->error());
                }

                return $this->completeTask();

            case 'show':
                $this->completeTask();

                $users = $users->show();

                $this->table(['Id', 'Name', 'Email'], $users);

                return 0;

            case 'token':
                $this->completeTask();

                $tokens = $users->token($email);

                $this->table([
                    'Id',
                    'Name',
                    'Expired',
                    'Created'
                ], collect($tokens)->map(function ($token) {
                    $token['revoked'] = $this->noYes($token['revoked']);

                    $token['id'] = Str::limit($token['id'], 50);

                    return $token;
                })->toArray());

                return 0;

            case 'revoke':
                if (!$users->revoke($email, $option1)) {
                    $this->error($users->error());
                }

                return $this->completeTask();
        }

        return $this->errorTask("Operation {$operation} unknown");
    }
}
