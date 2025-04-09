<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

abstract class Logger implements LoggerInterface
{
	abstract public function log($level, $message, array $context = []): void;

	public function alert(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::ALERT, $message, $context);
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::CRITICAL, $message, $context);
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::DEBUG, $message, $context);
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::EMERGENCY, $message, $context);
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::ERROR, $message, $context);
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::INFO, $message, $context);
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::NOTICE, $message, $context);
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		$this->addEntry(LogLevel::WARNING, $message, $context);
	}

	protected function addEntry(string $level, string $message, array $context): void
	{
		$message = "[DM PDK]: " . $message;
		$this->log($level, $message, $context);
	}
}
