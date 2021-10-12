<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Contracts\ValidatorRule;
use App\Rules\ResolvableRotation;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRotate implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'rotate' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ]
        ];
    }
}
