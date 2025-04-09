<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Facade;

use DeliveryMatch\Pdk\Logger\LoggerInterface;

/**
 * @method static void info(string $message)
 * @method static void warning(string $message)
 * @method static void error(string $message)
 * @see \DeliveryMatch\Pdk\Logger\LoggerInterface
 */
final class Logger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LoggerInterface::class;
    }
}
