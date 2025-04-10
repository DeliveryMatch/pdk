<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class AbstractLogger implements LoggerInterface
{
    /** @phpstan-ignore missingType.parameter */
    abstract public function log($level, $message, array $context = []): void;

    /** @phpstan-ignore missingType.parameter */
    public function alert($message, array $context = []): void
    {
        $this->addEntry(LogLevel::ALERT, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function critical($message, array $context = []): void
    {
        $this->addEntry(LogLevel::CRITICAL, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function debug($message, array $context = []): void
    {
        $this->addEntry(LogLevel::DEBUG, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function emergency($message, array $context = []): void
    {
        $this->addEntry(LogLevel::EMERGENCY, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function error($message, array $context = []): void
    {
        $this->addEntry(LogLevel::ERROR, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function info($message, array $context = []): void
    {
        $this->addEntry(LogLevel::INFO, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function notice($message, array $context = []): void
    {
        $this->addEntry(LogLevel::NOTICE, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    public function warning($message, array $context = []): void
    {
        $this->addEntry(LogLevel::WARNING, $message, $context);
    }

    /** @phpstan-ignore missingType.parameter */
    protected function addEntry(string $level, $message, array $context): void
    {
        $message = "[DM PDK]: " . $message;
        $this->log($level, $message, $context);
    }
}
