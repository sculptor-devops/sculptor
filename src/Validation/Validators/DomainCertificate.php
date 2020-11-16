<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Contracts\ValidatorRule;
use Sculptor\Agent\Enums\CertificatesTypes as Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCertificate implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'certificate' => [
                'required',
                'max:255',
                'in:' . implode(',', Enum::toArray())
            ]
        ];
    }
}
