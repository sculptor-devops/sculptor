<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\Vcs;
use Sculptor\Agent\Contracts\ValidatorRule;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainVcs implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'vcs' => [
                'required',
                'max:1024',
                new Vcs()
            ]
        ];
    }
}
