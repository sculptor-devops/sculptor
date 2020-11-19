<?php

namespace Sculptor\Agent\Actions;

use Illuminate\Support\Facades\Hash;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Repositories\UserRepository;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Users implements ActionInterface
{
    use Actionable;

    /**
     * @var UserRepository
     */
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function show(): array
    {
        return $this->users
            ->all()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ];
            })->toArray();
    }

    public function create(string $name, string $email, string $password): bool
    {
        $user = $this->users
            ->findWhere(['email' => $email])
            ->first();

        if ($user == null) {
            $this->users->create([
                'email' => $email,
                'name' => $name,
                'password' => Hash::make($password)
            ]);

            return true;
        }

        $user->update([
            'name' => $name,
            'password' => Hash::make($password)
        ]);

        return true;
    }

    public function delete(string $email): bool
    {
        $user = $this->users
            ->findWhere(['email' => $email])
            ->first();

        if ($user == null) {
            return false;
        }

        $user->delete();

        return true;
    }

    public function password(string $email, string $password): bool
    {
        $user = $this->users
            ->findWhere(['email' => $email])
            ->first();

        if ($user == null) {
            return false;
        }

        $user->update(['password' => Hash::make($password)]);

        return true;
    }

    public function token(string $email): array
    {
        $user = $this->users
            ->findWhere(['email' => $email])
            ->first();

        if ($user == null) {
            return [];
        }

        return $user->tokens()
            ->get()
            ->map(function ($token) {
                $name = $token->client()->first();

                if ($name != null) {
                    $name = $token->client()->first()->name;
                }
                return [
                'id' => $token->id,
                'name' => $name ?? 'Unknown',
                'revoked' => $token->revoked,
                'created_at' => $token->created_at
                ];
            })->toArray();
    }

    public function revoke(string $email, string $token): bool
    {
        $user = $this->users
            ->findWhere(['email' => $email])
            ->first();

        if ($user == null) {
            return false;
        }

        $token = $user->tokens()
            ->where('id', $token)
            ->first();

        if ($token == null) {
            return false;
        }

        $token->revoke();

        return true;
    }
}
