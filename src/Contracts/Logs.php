<?php

namespace Sculptor\Agent\Contracts;

use Psr\Log\LoggerInterface;

interface Logs
{
    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function actions(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function security(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function backup(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function batch(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function login(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function job(array $context = []): LoggerInterface;
}
