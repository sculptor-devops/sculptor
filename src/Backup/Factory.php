<?php

namespace Sculptor\Agent\Backup;

use InvalidArgumentException;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Factory
{
    /**
     * @var array
     */
    private $lookup;

    public function __construct(array $lookup)
    {
        $this->lookup = $lookup;
    }

    /**
     * @param Backup $backup
     * @return BackupInterface
     * @throws Exception
     */
    public function make(Backup $backup): BackupInterface
    {
        if (array_key_exists($backup->type, $this->lookup)) {
            return $this->lookup[$backup->type];
        }

        throw new InvalidArgumentException("Invalid backup type {$backup->type}");
    }
}
