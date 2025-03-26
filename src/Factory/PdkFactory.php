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
use function DI\value;

class PdkFactory
{
    public static function create(int $clientId, string $apiKey, string $environment, Cache $cache, array $configs): PdkInterface
    {
        $instance = new self();
        $container = $instance->setupContainer($clientId, $apiKey, $environment, $configs);

        $pdk = new Pdk($container, $cache);

        Facade::setPdkInstance($pdk);

        return $pdk;
    }

    private function setupContainer(int $clientId, string $apiKey, string $environment ,array $configs): Container
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(
            $configs,
            [
                PdkInterface::class => \DI\autowire(Pdk::class),
                "clientId" => value($clientId),
                "api" => new Client($apiKey, $clientId, $environment === "production")
            ]
        );

        return $builder->build();
    }
}
