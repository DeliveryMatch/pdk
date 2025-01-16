<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DI\Container;

class Pdk implements PdkInterface
{
    public function __construct(protected Container $container)
    {
    }

    public function get(string $key): mixed
    {
        return $this->container->get($key);
    }

    public function has(string $key): bool
    {
        return $this->container->has($key);
    }
}
