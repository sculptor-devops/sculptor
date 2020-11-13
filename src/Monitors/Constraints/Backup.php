<?php

namespace Sculptor\Agent\Monitors\Constraints;

use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Contracts\Constraint;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Monitors\Parametrizer;
use Sculptor\Agent\Repositories\BackupRepository;

class Backup implements Constraint
{
    /**
     * @var array
     */
    private $context = [];

    /**
     * @var bool
     */
    private $act = false;
    /**
     * @var BackupRepository
     */
    private $backups;
    /**
     * @var Factory
     */
    private $factory;

    public function __construct(BackupRepository $backups, Factory $factory)
    {
        $this->backups = $backups;

        $this->factory = $factory;
    }

    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param string $threshold
     * @return bool
     * @throws \Exception
     */
    public function threshold(bool $alarmed, string $rearm, string $threshold): bool
    {
        // id::older
        $value = true;

        $parameters = new Parametrizer($threshold);

        $limit = now()->subDays($parameters->last())->timestamp;

        $backup = $this->backups->byId($parameters->first());

        $tag = $this->factory->tag()
            ->extensions($backup->type, '', 'zip');

        $archive = $this->factory->archive($backup->archive);

        $archives = collect($archive->list($backup->destination));

        if ($archives
            ->where('timestamp', '<', $limit)
            ->where('size', '>', 0)
            ->filter(function ($item) use($tag, $backup) {
                return $tag->match($backup->name(), $item['basename']);
            })
            ->count()) {
            $value = false;
        }

        $evaluation = $this->evaluate($alarmed, $rearm, $value);

        if ($evaluation && $this->act) {
            Logs::batch()->notice("Backup {$backup->id} archive problem");
        }

        $this->context = [
            'value' => $value,
            'alarmed' => $alarmed,
            'rearm' => $rearm,
            'backup' => $backup->id
        ];

        return $evaluation;
    }

    /**
     * @param bool $alarmed
     * @param string $rearm
     * @param bool $value
     * @return bool
     */
    private function evaluate(bool $alarmed, string $rearm, bool $value): bool
    {
        switch ($rearm) {
            case 'auto':
                $this->act = $value;

                break;

            case 'manual':
                $this->act = $value && !$alarmed;

                break;
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function act(): bool
    {
        return $this->act;
    }

    /**
     * @return array
     */
    public function context(): array
    {
        return $this->context;
    }
}
