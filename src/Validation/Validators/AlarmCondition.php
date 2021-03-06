<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\ResolvableCondition;
use Sculptor\Agent\Contracts\ValidatorRule;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class AlarmCondition implements ValidatorRule
{
    /**
     * @return array[]
     */
    public function rule(): array
    {
        return [
            'condition' => [
                'required',
                'max:255',
                new ResolvableCondition('\\Sculptor\\Agent\\Monitors\\Conditions')
            ]
        ];
    }
}
