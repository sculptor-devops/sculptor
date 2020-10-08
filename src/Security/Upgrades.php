<?php

namespace Sculptor\Agent\Security;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sculptor\Foundation\Services\Daemons;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Upgrades
{
    /**
     * @var Daemons
     */
    private $daemons;
    /**
     * @var string
     */
    private $service = 'unattended-upgrades';
    /**
     * @var string
     */
    private $startTag = 'Log started:';
    /**
     * @var string
     */
    private $endTag = 'Log ended:';
    /**
     * @var string
     */
    private $filename = '/var/log/unattended-upgrades/unattended-upgrades-dpkg.log';
    /**
     * @var Carbon
     */
    private $startDate = null;
    /**
     * @var Carbon
     */
    private $endDate = null;
    /**
     * @var bool
     */
    private $opened = false;

    /**
     * Upgrades constructor.
     * @param Daemons $daemons
     */
    public function __construct(Daemons $daemons)
    {
        $this->daemons = $daemons;
    }

    /**
     * @return array
     */
    private function lines(): array
    {
        if (!File::exists($this->filename)) {
            return [];
        }

        $content = File::get($this->filename);

        return splitNewLine($content);
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function filename(string $filename): Upgrades
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function events(): array
    {
        $result = [];

        foreach ($this->lines() as $line) {
            if (Str::startsWith($line, $this->startTag)) {
                $result[] = new Carbon(Str::after($line, ':'));
            }
        }

        return $result;
    }

    /**
     * @param Carbon|null $date
     * @return UpgradeLog
     * @throws Exception
     */
    public function parse(Carbon $date = null): UpgradeLog
    {
        $result = [];

        if ($date == null) {
            $date = now();
        }

        foreach ($this->lines() as $line) {
            if ($this->parseStart($line, $date)) {
                continue;
            }

            if ($this->parseEnd($line)) {
                continue;
            }

            if ($this->opened && trim($line) != '') {
                $result[] = trim($line);
            }
        }

        return new UpgradeLog($result, $this->startDate, $this->endDate);
    }

    /**
     * @param string $line
     * @param Carbon $date
     * @return bool
     * @throws Exception
     */
    private function parseStart(string $line, Carbon $date): bool
    {
        if (Str::startsWith($line, $this->startTag) && !$this->opened) {
            $date = new Carbon(Str::after($line, ':'));

            $this->opened = $date->day == $date->day &&
                $date->month == $date->month &&
                $date->year == $date->year;

            $this->startDate = ($this->startDate == null && $this->opened) ? $date : $this->startDate;

            return true;
        }

        return false;
    }

    /**
     * @param string $line
     * @return bool
     * @throws Exception
     */
    private function parseEnd(string $line): bool
    {
        if (Str::startsWith($line, $this->endTag) && $this->opened) {
            $this->opened = false;

            $this->endDate = new Carbon(Str::after($line, ':'));

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function enable(): bool
    {
        return $this->daemons->enable($this->service);
    }

    /**
     * @return bool
     */
    public function active(): bool
    {
        return $this->daemons->active($this->service);
    }
}
