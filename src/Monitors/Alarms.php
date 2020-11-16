<?php

namespace Sculptor\Agent\Monitors;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Contracts\AlarmAction;
use Sculptor\Agent\Contracts\AlarmCondition;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Alarm;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Alarms
{
    /**
     * @var string
     */
    public const NAMESPACE = 'Sculptor\\Agent\\Monitors\\';

    /**
     * @var AlarmAction
     */
    private $action;
    /**
     * @var Alarm
     */
    private $alarm;
    /**
     * @var AlarmCondition
     */
    private $condition;
    /**
     * @var string
     */
    private $threshold;

    public function __construct(Alarm $alarm)
    {
        $this->alarm = $alarm;

        $this->action = resolve($this->resolveType($alarm->type));

        $this->condition = resolve($this->resolveConstraint($alarm->condition));
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
        return Alarms::NAMESPACE . 'Actions\\' . $this->normalize($type);
    }

    /**
     * @param string|null $type
     * @return string
     */
    private function resolveConstraint(?string $type): string
    {
        $name = Str::before($type, Parametrizer::SEPARATOR);

        $this->threshold = Str::after($type, Parametrizer::SEPARATOR);

        return Alarms::NAMESPACE . 'Conditions\\' . $this->normalize($name);
    }

    /**
     * @throws Exception
     */
    private function valid(): void
    {
        if ($this->alarm == null) {
            throw new Exception("Unable to find {$this->alarm->type} alarm type");
        }

        if ($this->condition == null) {
            throw new Exception("Unable to find {$this->alarm->condition} constraint type");
        }

        if ($this->alarm->to == null) {
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

            if ($this->condition->threshold($this->alarm->alarm, $this->alarm->rearm, $this->threshold)) {
                $this->alarmed();
            }

            if ($this->condition->act()) {
                $this->action->emit($this->alarm->to, $this->alarm->message, $this->condition->context());
            }

            return true;
        } catch (Exception $e) {
            Logs::batch()->report($e);

            $this->alarm->update([
                'error' => Str::limit($e->getMessage(), 250)
            ]);
        }

        return false;
    }

    private function alarmed(): void
    {
        if ($this->alarm->alarm) {
            $this->alarm->update([
                'alarm_until' => now(),
                'error' => null
            ]);

            return;
        }

        $this->alarm->update([
            'alarm' => true,
            'alarm_at' => now(),
            'alarm_until' => now(),
            'error' => null
        ]);
    }
}
