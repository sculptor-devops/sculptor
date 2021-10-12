<?php

namespace Sculptor\Agent\Backup\Rotations;

use Sculptor\Agent\Backup\Contracts\Rotation;
use Sculptor\Agent\Backup\Contracts\Archive;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Days implements Rotation
{
    /**
     * @var Archive
     */
    private $archive;

    public function __construct(
        Archive $archive
    ) {
        $this->archive = $archive;
    }

    public function name(): string
    {
        return 'days';
    }

    public function rotate(array $catalogs, int $number): array
    {
        $timestamp = now()->subDays($number);

        $purgiable = collect($catalogs)
            ->sortByDesc('timestamp')
            ->where('timestamp', '>', $timestamp->timestamp)
            ->toArray();

        foreach($purgiable as $file) {
            $this->archive->delete($file);
        }

        return $purgiable;
    }
}
