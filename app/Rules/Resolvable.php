<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Resolvable implements Rule
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
        return class_exists("{$this->namespace}\\" . Str::ucfirst($value));
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
