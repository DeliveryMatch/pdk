<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Common\Pdk;
use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Facade\Facade;
use DeliveryMatch\Sdk\Client;
use DI\ContainerBuilder;

class PdkFactory
{
    public static function create(int $clientId, string $apiKey, array ...$configs): PdkInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(
            ["api" => new Client($apiKey, $clientId)],
            ...$configs
        );
        $container = $builder->build();

        $pdk = new Pdk($container);

        Facade::setPdkInstance($pdk);

        return $pdk;
    }
}
