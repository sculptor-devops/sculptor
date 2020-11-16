<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

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
