<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator as Validation;

class Vcs implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $validated = Validation::make(["url" => $value], [ 'url' => [ 'required', 'url']]);

        if (!$validated->fails()) {
            return true;
        }

        if (preg_match('/^(([A-Za-z0-9]+@|http(|s)\:\/\/)|(http(|s)\:\/\/[A-Za-z0-9]+@))([A-Za-z0-9.]+(:\d+)?)(?::|\/)([\d\/\w.-]+?)(\.git){1}$/i', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'This is not a valid git url or ssh';
    }
}
