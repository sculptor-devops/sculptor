<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Sculptor\Agent\Enums\LogContextLevel;
use Sculptor\Agent\Repositories\EventRepository;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogsContext implements LoggerInterface
{
    /**
     * @var array
     */
    private $context;
    /**
     * @var EventRepository
     */
    private $repository;

    private function merge(array $context = []): array
    {
        return array_merge($context, $this->context);
    }

    private function event(string $message, string $level, array $context, string $payload = null): void
    {
        $this->repository->create([
            'message' => Str::limit($message, 250),
            'tag' => $context['tag'],
            'level' => $level,
            'context' => $context,
            'payload' => $payload,
        ]);
    }


    public function __construct(array $context, EventRepository $repository)
    {
        $this->context = $context;

        $this->repository = $repository;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::emergency($message, $context);

        $this->event($message, LogContextLevel::EMERGENCY, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::alert($message, $context);

        $this->event($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::critical($message, $context);

        $this->event($message, LogContextLevel::CRITICAL, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::error($message, $context);

        $this->event($message, LogContextLevel::ERROR, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::warning($message, $context);

        $this->event($message, LogContextLevel::WARNING, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::notice($message, $context);

        $this->event($message, LogContextLevel::NOTICE, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $context = $this->merge($context);

        Log::info($message, $context);

        $this->event($message, LogContextLevel::INFO, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $context = $this->merge($context);

        if (config('app.debug')) {
            Log::debug($message, $context);

            $this->event($message, LogContextLevel::DEBUG, $context);
        }
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $context = $this->merge($context);

        Log::log($level, $message, $context);

        $this->event($message, LogContextLevel::LOG, $context);
    }

    /**
     * @param Throwable $e
     */
    public function report(Throwable $e): void
    {
        report($e);

        $this->event($e->getMessage(), LogContextLevel::ERROR, $this->context, $e->getTraceAsString());
    }
}
