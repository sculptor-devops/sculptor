<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Psr\Log\LoggerInterface;

class Logs
{
    public const ACTIONS = 'actions';
    public const SECURITY = 'security';
    public const BACKUP = 'backup';
    public const LOGIN = 'login';
    public const BATCH = 'batch';

    /*
     * @var string
     */
    /**
     * @var string
     */
    private $tag;

    public function __construct(string $tag = Logs::ACTIONS)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    private function from(): string
    {
        $from = Request::ip();

        if ($from == null) {
            return 'unknown';
        }

        return $from;
    }

    /**
     * @param string $tag
     * @return LogsContext
     */
    public function tag(string $tag): LogsContext
    {
        $this->tag = $tag;

        return new LogsContext($this->context());
    }

    /**
     * @return array
     */
    public function context(): array
    {
        return [ 'ip' => $this->from(), 'tag' => $this->tag ];
    }

    /**
     * @return LogsContext
     */
    public static function actions(): LogsContext
    {
        $logs =  new Logs();

        return new LogsContext($logs->context());
    }

    /**
     * @return LogsContext
     */
    public static function security(): LogsContext
    {
        $logs =  new Logs(Logs::SECURITY);

        return new LogsContext($logs->context());
    }

    /**
     * @return LogsContext
     */
    public static function backup(): LogsContext
    {
        $logs =  new Logs(Logs::BACKUP);

        return new LogsContext($logs->context());
    }

    /**
     * @return LogsContext
     */
    public static function batch(): LogsContext
    {
        $logs =  new Logs(Logs::BATCH);

        return new LogsContext($logs->context());
    }

    /**
     * @return LogsContext
     */
    public static function login(): LogsContext
    {
        $logs =  new Logs(Logs::LOGIN);

        return new LogsContext($logs->context());
    }
}
