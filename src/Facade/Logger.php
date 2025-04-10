<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Facade;

use DeliveryMatch\Pdk\Logger\PdkLoggerInterface;

/**
 * @method static void log($level, $message, array $context = [])
 * @method static void alert($message, array $context = [])
 * @method static void critical($message, array $context = [])
 * @method static void debug($message, array $context = [])
 * @method static void emergency($message, array $context = [])
 * @method static void error($message, array $context = [])
 * @method static void info($message, array $context = [])
 * @method static void notice($message, array $context = [])
 * @method static void warning($message, array $context = [])
 * @see \DeliveryMatch\Pdk\Logger\PdkLoggerInterface
 */
final class Logger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PdkLoggerInterface::class;
    }
}
