<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Common\Repository;
use DeliveryMatch\Pdk\Common\Pdk;
use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Facade\Facade;
use DeliveryMatch\Sdk\Client;
use DeliveryMatch\Sdk\HttpClient\ApiEnvironment;
use DeliveryMatch\Sdk\HttpClient\Builder;
use DI\Container;
use DI\ContainerBuilder;

use function DI\value;

class PdkFactory
{
    public static function create(int $clientId, string $apiKey, ApiEnvironment $environment, array $configs, ?Builder $httpClientBuilder = null): PdkInterface
    {
        $instance = new self();
        $container = $instance->setupContainer($clientId, $apiKey, $environment, $configs, $httpClientBuilder);

        $pdk = new Pdk($container, $container->get(Repository::class));

        Facade::setPdkInstance($pdk);

        return $pdk;
    }

    private function setupContainer(int $clientId, string $apiKey, ApiEnvironment $environment, array $configs, ?Builder $httpClientBuilder = null): Container
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(
            $configs,
            [
                PdkInterface::class => \DI\autowire(Pdk::class),
                "clientId" => value($clientId),
                "api" => new Client($apiKey, $clientId, $environment, $httpClientBuilder)
            ]
        );

        return $builder->build();
    }
}
