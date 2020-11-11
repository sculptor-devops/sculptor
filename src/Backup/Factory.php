<?php

namespace Sculptor\Agent\Backup;

use Exception;
use Sculptor\Agent\Backup\Archives\Dropbox;
use Sculptor\Agent\Backup\Archives\Local;
use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Compression\Zip;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Backup\Contracts\Rotation;
use Sculptor\Agent\Backup\Subjects\Blueprint;
use Sculptor\Agent\Backup\Subjects\Database;
use Sculptor\Agent\Backup\Subjects\Domain;
use Sculptor\Agent\Blueprint as BlueprintService;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupArchiveType;
use Sculptor\Agent\Enums\BackupRotationType;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;
use Sculptor\Agent\Backup\Rotations\Number;
use Sculptor\Agent\Backup\Rotations\Days;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Factory
{

    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var BlueprintService
     */
    private $blueprint;
    /**
     * @var Tag
     */
    private $tag;

    public function __construct(Configuration $configuration, BlueprintService $blueprint, Tag $tag)
    {
        $this->configuration = $configuration;

        $this->blueprint = $blueprint;

        $this->tag = $tag;
    }

    /**
     * @param Backup $backup
     * @return BackupInterface
     * @throws Exception
     */
    public function make(Backup $backup): BackupInterface
    {
        $archive = $backup->archive;

        switch ($backup->type) {
            case BackupType::DATABASE:
                return new Database($this->configuration, $this->archive($archive), $this->compressor(), $this->tag);

            case BackupType::DOMAIN:
                return new Domain($this->configuration, $this->archive($archive), $this->compressor(), $this->tag);

            case BackupType::BLUEPRINT:
                return new Blueprint($this->configuration, $this->blueprint, $this->archive($archive),
                    $this->compressor(), $this->tag);
        }

        throw new Exception("Invalid backup type {$backup->type}");
    }

    /**
     * @param string|null $type
     * @return Archive
     * @throws Exception
     */
    public function archive(?string $type = null): Archive
    {
        if ($type == null) {
            $type = $this->configuration->get('sculptor.backup.drivers.default');
        }

        switch ($type) {
            case BackupArchiveType::LOCAL:
                return new Local();

            case BackupArchiveType::S3:
                return new S3($this->configuration);

            case BackupArchiveType::DROPBOX:
                return new Dropbox($this->configuration);

        }

        throw new Exception("Invalid {$type} archive driver");
    }

    /**
     * @return Compressor
     */
    public function compressor(): Compressor
    {
        return new Zip();
    }

    /**
     * @param string|null $type
     * @return Rotation
     * @throws Exception
     */
    public function rotation(?string $type): Rotation
    {
        if ($type == null) {
            $type = $this->configuration->get('sculptor.backup.rotation');
        }

        switch ($type) {
            case BackupRotationType::NUMBER:
                return new Number();

            case BackupRotationType::DAYS:
                return new Days();
        }

        throw new Exception("Invalid {$type} rotation policy");
    }
}
