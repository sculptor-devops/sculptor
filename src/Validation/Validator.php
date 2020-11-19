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
    /**
     * @var string
     */
    public const NAMESPACE = 'Sculptor\\Agent\\Validation\\Validators\\';

    /**
     * @var string|null
     */
    private $scope;

    /**
     * @var string
     */
    private $error;

    /**
     * Validator constructor.
     * @param string|null $scope
     */
    public function __construct(string $scope = null)
    {
        $this->scope = $scope;
    }

    /**
     * @param string $scope
     * @return Validator
     */
    public static function make(string $scope): Validator
    {
        return new Validator($scope);
    }

    /**
     * @param string $name
     * @return array
     */
    public function rule(string $name): array
    {
        $rule = resolve(Validator::NAMESPACE . "{$this->scope}" . Str::ucfirst($name));

        return $rule->rule();
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
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

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }
}
