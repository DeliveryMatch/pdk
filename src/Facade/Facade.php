<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Facade;

use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Exception\InvalidStateException;

abstract class Facade
{
    protected static ?PdkInterface $pdk;

    public static function __callStatic(string $name, array $arguments): mixed
    {
        return static::getFacadeRoot()
            ->$name(
                ...$arguments
            );
    }

    public static function getPdkInstance(): ?PdkInterface
    {
        return self::$pdk;
    }

    public static function setPdkInstance(?PdkInterface $pdk): void
    {
        self::$pdk = $pdk;
    }

    abstract protected static function getFacadeAccessor(): string;

    /**
     * @throws InvalidStateException
     */
    protected static function getFacadeRoot(): mixed
    {
        if (!static::$pdk) {
            throw new InvalidStateException('The Pdk instance has not been set. Please ensure that setPdkInstance() is called with a valid PdkInterface instance before accessing the Facade.');
        }

        return static::$pdk->get(static::getFacadeAccessor());
    }
}
