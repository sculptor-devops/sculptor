<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Contracts\LogContext;
use Sculptor\Agent\Enums\LogContextLevel;
use Sculptor\Agent\Repositories\EventRepository;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogsContext implements LogContext
{
    /**
     * @var array
     */
    private $context;
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @param array $context
     * @return array
     */
    private function merge(array $context = []): array
    {
        return array_merge($context, $this->context);
    }

    /**
     * @param string $message
     * @param string $level
     * @param array $context
     * @param string|null $payload
     * @throws ValidatorException
     */
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
     * @throws ValidatorException
     */
    public function emergency(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::emergency($message, $context);

        $this->event($message, LogContextLevel::EMERGENCY, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function alert(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::alert($message, $context);

        $this->event($message, LogContextLevel::ALERT, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function critical(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::critical($message, $context);

        $this->event($message, LogContextLevel::CRITICAL, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function error(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::error($message, $context);

        $this->event($message, LogContextLevel::ERROR, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function warning(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::warning($message, $context);

        $this->event($message, LogContextLevel::WARNING, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function notice(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::notice($message, $context);

        $this->event($message, LogContextLevel::NOTICE, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function info(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::info($message, $context);

        $this->event($message, LogContextLevel::INFO, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @throws ValidatorException
     */
    public function debug(string $message, array $context = array()): void
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
     * @throws ValidatorException
     */
    public function log(int $level, string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::log($level, $message, $context);

        $this->event($message, LogContextLevel::LOG, $context);
    }

    /**
     * @param Throwable $e
     * @throws ValidatorException
     */
    public function report(Throwable $e): void
    {
        report($e);

        $this->event($e->getMessage(), LogContextLevel::ERROR, $this->context, $e->getTraceAsString());
    }
}
