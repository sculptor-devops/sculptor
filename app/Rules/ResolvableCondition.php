<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use Sculptor\Agent\Monitors\Parametrizer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ResolvableCondition implements Rule
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * Create a new rule instance.
     *
     * @param string $namespace
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $name = Str::before(Str::ucfirst($value), Parametrizer::SEPARATOR);

        return class_exists("{$this->namespace}\\{$name}");
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The class cannot be resolved.';
    }
}
