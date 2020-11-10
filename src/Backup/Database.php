<?php

namespace Sculptor\Agent\Backup;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;
use Sculptor\Agent\Backup\Dumper\MySql;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupDatabaseType;
use Sculptor\Agent\Repositories\Entities\Backup as Item;
use Sculptor\Agent\Repositories\Entities\Database as DatabaseItem;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Database implements BackupInterface
{
    /**
     * @var Archive
     */
    private $archive;
    /**
     * @var string
     */
    private $tmp;
    /**
     * @var Compressor
     */
    private $compressor;
    /**
     * @var Tag
     */
    private $tag;
    /**
     * @var int
     */
    private $size;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, Archive $archive, Compressor $compressor, Tag $tag)
    {
        $this->configuration = $configuration;

        $this->tmp = $configuration->get('sculptor.backup.temp');

        $this->archive = $archive;

        $this->compressor = $compressor;

        $this->tag = $tag->extensions('database', 'sql', $this->compressor->extension());
    }

    /**
     * @param DatabaseItem $database
     * @return array
     * @throws Exception
     */
    private function config(DatabaseItem $database): array
    {
        $user = $database->users()
            ->first();

        if ($user == null) {
            throw new Exception("No user defined for database {$database->name}");
        }

        return [
            'DB_HOST' => $this->configuration->get("sculptor.database.drivers.{$database->driver}.host"),
            'DB_PORT' => $this->configuration->get("sculptor.database.drivers.{$database->driver}.port"),
            'DB_DATABASE' => $database->name,
            'DB_USERNAME' => $user->name,
            'DB_PASSWORD' => $user->password
        ];
    }

    /**
     * @param string $name
     * @param string $destination
     */
    private function move(string $name, string $destination): void
    {
        $to = $this->tag->destination($name, $destination);

        $compressed = $this->tag->compressed($name);

        $this->size = File::size($compressed);

        $this->archive
            ->create($destination)
            ->put($to, File::get($compressed));
    }

    /**
     * @param string $filename
     * @param DatabaseItem $database
     * @throws CannotStartDump
     * @throws DumpFailed
     * @throws Exception
     */
    private function dump(string $filename, DatabaseItem $database): void
    {
        $config = $this->config($database);

        switch ($database->driver) {
            case BackupDatabaseType::MYSQL:
                $dumper = new MySql($config);

                break;

            default:
                throw new Exception("Invalid database backup driver {$database->driver}");
        }

        $dumper->dump($filename);
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function create(Item $backup): bool
    {
        $database = $backup->database;

        $filename = $this->tag->temp($database->name);

        $compressed = $this->tag->compressed($database->name);

        $this->dump($filename, $database);

        $this->compressor
            ->create($compressed)
            ->file($filename)
            ->close();

        $this->move($database->name, $backup->destination);

        return true;
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function rotate(Item $backup): bool
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return array
     * @throws Exception
     */
    public function archives(Item $backup): array
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function check(Item $backup): bool
    {
        if ($backup->database == null) {
            throw new Exception("Backup must define a database");
        }

        if ($backup->database->users()->count() == 0) {
            throw new Exception("Backup must define a database with at least one user");
        }

        if ($backup->destination == null) {
            throw new Exception("Backup must define a destination");
        }

        return true;
    }

    public function clean(Item $backup): bool
    {
        $compressed = $this->tag->compressed($backup->database->name);

        $temp = $this->tag->temp($backup->database->name);

        if (File::extension($compressed)) {
            File::delete($compressed);
        }

        if (File::extension($temp)) {
            File::delete($temp);
        }

        return true;
    }

    public function size(): int
    {
        return $this->size;
    }
}
