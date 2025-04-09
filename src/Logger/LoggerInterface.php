<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Logger;

interface LoggerInterface
{
    public static function info(string $message): void;
    public static function warning(string $message): void;
    public static function error(string $message): void;
}
