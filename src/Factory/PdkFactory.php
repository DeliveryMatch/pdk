<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Common\Cache;
use DeliveryMatch\Pdk\Common\Pdk;
use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Facade\Facade;
use DeliveryMatch\Sdk\Client;
use DI\Container;
use DI\ContainerBuilder;

class PdkFactory
{
    public static function create(int $clientId, string $apiKey, Cache $cache, array ...$configs): PdkInterface
    {
        $instance = new PdkFactory();
        $container = $instance->setupContainer($clientId, $apiKey, $cache, ...$configs);

        $pdk = new Pdk($container);

        Facade::setPdkInstance($pdk);

        return $pdk;
    }

    private function setupContainer(int $clientId, string $apiKey, Cache $cache, array ...$configs): Container
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(
            [
                "api" => new Client($apiKey, $clientId),
                "cache" => $cache,
                PdkInterface::class => \DI\autowire(Pdk::class)
            ],
            ...$configs
        );

        return $builder->build();
    }
}
