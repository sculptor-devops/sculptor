<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface DomainAction
{
    public function compile(Domain $domain): bool;

    public function delete(Domain $domain): bool;
}
