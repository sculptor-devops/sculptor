<?php

namespace Sculptor\Agent\Backup\Contracts;

use Sculptor\Agent\Backup\Tag;
use Sculptor\Agent\Repositories\Entities\Backup as Item;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Backup
{
    public function create(Item $backup): bool;

    public function clean(Item $backup): bool;

    public function rotate(Item $backup, bool $dry = false): array;

    public function archives(Item $backup): array;

    public function check(Item $backup): bool;

    public function size(): int;
}
