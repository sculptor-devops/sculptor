<?php


namespace Sculptor\Agent\Actions;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Monitors\Alarms as UserAlarm;
use Sculptor\Agent\Repositories\AlarmRepository;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Alarms implements ActionInterface
{
    use Actionable;

    /**
     * @var AlarmRepository
     */
    private $monitors;

    public function __construct(Action $action, AlarmRepository $monitors)
    {
        $this->action = $action;

        $this->monitors = $monitors;
    }

    public function create(string $type): bool
    {
        try {
            $this->monitors->create([
                'message' => Str::ucfirst("{$type} monitor"),
                'type' => $type
            ]);

            return true;
        } catch (Exception $e) {
            $this->action->report($e->getMessage());
        }

        return false;
    }

    public function delete(int $id): bool
    {
        try {
            $monitor = $this->monitors->byId($id);

            $monitor->delete();

            return true;
        } catch (Exception $e) {
            $this->action->report($e->getMessage());
        }

        return false;
    }

    public function setup(int $id, string $key, string $value): bool
    {
        try {
            $monitor = $this->monitors->byId($id);

            $monitor->update([
                "{$key}" => "{$value}"
            ]);

            return true;
        } catch (Exception $e) {
            $this->action->report($e->getMessage());
        }

        return false;
    }

    public function rearm(int $id): bool
    {
        try {
            $monitor = $this->monitors->byId($id);

            $monitor->update([
                'alarm' => false,
                'alarm_at' => null,
                'alarm_until' => null,
                'error' => null
            ]);

            return true;
        } catch (Exception $e) {
            $this->action->report($e->getMessage());
        }

        return false;
    }

    public function run(int $id): bool
    {
        try {
            $monitor = $this->monitors->byId($id);

            $alarm = new UserAlarm($monitor);

            if (!$alarm->run()) {
                throw new Exception("{$monitor->error}");
            }

            return true;
        } catch (Exception $e) {
            $this->action->report($e->getMessage());
        }

        return false;
    }

    public function show(): array
    {
        return $this->monitors->all()->toArray();
    }
}
