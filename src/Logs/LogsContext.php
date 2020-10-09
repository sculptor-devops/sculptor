<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Psr\Log\LoggerInterface;

class LogsContext implements LoggerInterface
{
    private $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    private function merge(array $context = []): array
    {
        return array_merge($context, $this->context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        Log::emergency($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        Log::alert($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        Log::critical($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        Log::error($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        Log::warning($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        Log::notice($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     * @return $this
     */
    public function info($message, array $context = array())
    {
        Log::info($message, $this->merge($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        Log::debug($message, $this->merge($context));
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        Log::log($level, $message, $this->merge($context));
    }
}
