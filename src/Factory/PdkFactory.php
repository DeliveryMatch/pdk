<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeiveryMatch\Pdk\Facade\Facade;
use DeliveryMatch\Client;
use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Common\Pdk;
use DI\ContainerBuilder;

class PdkFactory
{
    public static function create(int $clientId, string $apiKey, ...$configs): PdkInterface
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
