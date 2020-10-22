<?php

namespace Sculptor\Agent\Backup\Contracts;

use Sculptor\Agent\Backup\Tag;
use Sculptor\Agent\Repositories\Entities\Backup as Item;

interface Backup
{
    public function create(Item $backup): bool;

    public function clean(Item $backup): bool;

    public function rotate(Item $backup): bool;

    public function archives(Item $backup): array;

    public function check(Item $backup): bool;

    public function size(): int;
}
