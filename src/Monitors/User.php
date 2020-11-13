<?php

namespace Sculptor\Agent\Monitors;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Contracts\Alarm;
use Sculptor\Agent\Contracts\Constraint;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Monitor;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class User
{
    /**
     *
     */
    public const NAMESPACE = 'Sculptor\\Agent\\Monitors\\';

    /**
     * @var Monitor
     */
    private $monitor;
    /**
     * @var Alarm
     */
    private $alarm;
    /**
     * @var Constraint
     */
    private $constraint;
    /**
     * @var string
     */
    private $threshold;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;

        $this->alarm = resolve($this->resolveType($monitor->type));

        $this->constraint = resolve($this->resolveConstraint($monitor->constraint));
    }

    /**
     * @param string|null $name
     * @return string
     */
    private function normalize(?string $name): string
    {
        return Str::of($name)->camel()
            ->ucfirst()
            ->__toString();
    }

    /**
     * @param string|null $type
     * @return string
     */
    private function resolveType(?string $type): string
    {
        return User::NAMESPACE . 'Alarms\\' . $this->normalize($type);
    }

    /**
     * @param string|null $type
     * @return string
     */
    private function resolveConstraint(?string $type): string
    {
        $name = Str::before($type, Parametrizer::SEPARATOR);

        $this->threshold = Str::after($type, Parametrizer::SEPARATOR);

        return User::NAMESPACE . 'Constraints\\' . $this->normalize($name);
    }

    /**
     * @throws Exception
     */
    private function valid(): void
    {
        if ($this->alarm == null) {
            throw new Exception("Unable to find {$this->monitor->type} alarm type");
        }

        if ($this->constraint == null) {
            throw new Exception("Unable to find {$this->monitor->constraint} constraint type");
        }

        if ($this->monitor->to == null) {
            throw new Exception("No destination given");
        }
    }

    /**
     * @throws Exception
     */
    public function run(): bool
    {
        try {
            $this->valid();

            if ($this->constraint->threshold($this->monitor->alarm, $this->monitor->rearm, $this->threshold)) {
                $this->alarmed();
            }

            if ($this->constraint->act()) {
                $this->alarm->emit($this->monitor->to, $this->monitor->message, $this->constraint->context());
            }

            return true;
        } catch (Exception $e) {
            Logs::batch()->report($e);

            $this->monitor->update([
                'error' => Str::limit($e->getMessage(), 250)
            ]);
        }

        return false;
    }

    private function alarmed(): void
    {
        if ($this->monitor->alarm) {
            $this->monitor->update([
                'alarm_until' => now(),
                'error' => null
            ]);

            return;
        }

        $this->monitor->update([
            'alarm' => true,
            'alarm_at' => now(),
            'alarm_until' => now(),
            'error' => null
        ]);
    }
}
