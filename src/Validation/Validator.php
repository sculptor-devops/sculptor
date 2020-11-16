<?php

namespace Sculptor\Agent\Validation;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Exception;
use Illuminate\Support\Facades\Validator as Validation;
use Illuminate\Support\Str;

class Validator
{
    public const NAMESPACE = 'Sculptor\\Agent\\Validation\\Validators\\';

    /**
     * @var string|null
     */
    private $scope;

    private $error;

    public function __construct(string $scope = null)
    {
        $this->scope = $scope;
    }

    public static function make(string $scope): Validator
    {
        return new Validator($scope);
    }

    public function rule(string $name): array
    {
        $rule = resolve(Validator::NAMESPACE . "{$this->scope}" . Str::ucfirst($name));

        return $rule->rule();
    }

    public function validate(string $name, string $value): bool
    {
        try {
            $rule = $this->rule($name);

            $validated = Validation::make(["{$name}" => $value], $rule);

            if (!$validated->fails()) {
                return true;
            }

            $this->error = $validated->errors()->first();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        return false;
    }

    public function error(): string
    {
        return $this->error;
    }
}
