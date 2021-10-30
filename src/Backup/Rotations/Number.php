<?php

namespace Sculptor\Agent\Backup\Rotations;

use Sculptor\Agent\Backup\Contracts\Rotation;
use Sculptor\Agent\Backup\Contracts\Archive;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Number implements Rotation
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
        return 'number';
    }

    public function rotate(array $catalogs, int $number, string $destination, bool $dry = false): array
    {
        $catalogs = collect($catalogs)
            ->sortByDesc('timestamp');

        $this->archive->create($destination);

        if ($catalogs->count() < $number) {
            return [];
        }

        $pivot = $catalogs->take($number)->last();

        $purgeable = $catalogs->where('timestamp', '<', $pivot['timestamp'])
                ->toArray();

        if (!$dry) {
            foreach ($purgeable as $file) {
                $this->archive->delete($file['basename']);
            }
        }

        return $purgeable;
    }
}
