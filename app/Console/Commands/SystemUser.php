<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Repositories\UserRepository;
use Sculptor\Agent\Support\CommandBase;

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
     * @param UserRepository $users
     * @param PasswordGenerator $passwords
     * @return int
     * @throws ValidatorException
     */
    public function handle(UserRepository $users, PasswordGenerator $passwords): int
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

                $users->updateOrCreate([
                    'name' => $option1 ?? $email,
                    'email' => $email,
                    'password' => Hash::make($option2)
                ]);

                $this->completeTask();

                $this->warn("Password is {$option2}");

                return 0;

            case 'password':
                $user = $users->findWhere(['email' => $email])
                    ->first();

                if ($option1 == null) {
                    $option1 = $passwords->create();
                }

                if ($user == null) {
                    return $this->errorTask("User {$email} not found");
                }

                $user->update([ 'password' => Hash::make($option1) ]);

                $this->completeTask();

                $this->warn("Password is {$option1}");

                return 0;

            case 'delete':
                $user = $users->findWhere(['email' => $email])
                    ->first();

                if ($user == null) {
                    return $this->errorTask("User {$email} not found");
                }

                $user->delete();

                return $this->completeTask();

            case 'show':
                $this->completeTask();

                $list = $users->all();

                $this->table(['Name', 'Email'], $list->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email
                    ];
                })->toArray());

                return 0;
        }

        return $this->errorTask("Operation {$operation} unknown");
    }
}
