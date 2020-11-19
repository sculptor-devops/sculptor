<?php

namespace Sculptor\Agent\Support;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Sculptor\Agent\Exceptions\StatusMachineException;
use Sculptor\Agent\Facades\Logs;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class StateMachine
{
    /**
     * @var array
     */
    protected $from = [
    ];

    public function __construct(array $from)
    {
        $this->from = $from;
    }

    /**
     * @param string $to
     * @return array
     * @throws Exception
     */
    private function available(string $to): array
    {
        if (!array_key_exists($to, $this->from)) {
            throw new Exception("Invalid status {$to}");
        }

        return $this->from[$to];
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     * @throws Exception
     */
    public function can(string $from, string $to): bool
    {
        $available = $this->available($from);

        if (in_array($to, $available) || count($available) == 0 || $available == null) {
            return true;
        }

        throw new StatusMachineException($from, $to);
    }

    /**
     * @param string $to
     * @return string
     * @throws Exception
     */
    public function next(string $to): string
    {
        $statuses = $this->available($to);

        if (count($statuses) == 0) {
            return "Can go in any status";
        }

        return implode(', ', $statuses);
    }

    /**
     * @param Model $subject
     * @param string $next
     * @return bool
     * @throws Exception
     */
    public function change(Model $subject, string $next): bool
    {
        if ($subject['status'] == null) {
            throw new Exception("Status machine subject must have a status");
        }

        if ($subject['name'] == null) {
            throw new Exception("Status machine subject must have a name");
        }

        if ($this->can($subject['status'], $next)) {
            $subject->update(['status' => $next]);

            Logs::actions()->notice("{$subject['name']} changed status from {$subject['status']} to {$next}");

            return true;
        }

        return false;
    }
}
