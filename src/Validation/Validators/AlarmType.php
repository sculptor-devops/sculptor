<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\Resolvable;
use Sculptor\Agent\Contracts\ValidatorRule;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class AlarmType implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'type' => [
                'required',
                'max:255',
                new Resolvable('\\Sculptor\\Agent\\Monitors\\Actions')
            ]
        ];
    }
}
