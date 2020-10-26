<?php


namespace Sculptor\Agent\Logs;

use Monolog\Handler;

class LogToUser
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            dd($handler);

            $handler->se
        }
    }
}
