<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Lorisleiva\CronTranslator\CronTranslator;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Cron implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            return !empty(CronTranslator::translate($value));
        } catch (Exception $e) {

            return false;
        }
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
        return 'Invalid CRON.';
    }
}
