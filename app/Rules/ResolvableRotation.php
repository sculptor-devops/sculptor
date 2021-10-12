<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use Sculptor\Agent\Support\Parametrizer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ResolvableRotation implements Rule
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

        $name = Str::of($name)
            ->camel()
            ->ucfirst();

        return class_exists("{$this->namespace}\\{$name}");
    }

    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
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
