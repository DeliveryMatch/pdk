<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeiveryMatch\Pdk\Facade\Facade;
use DeliveryMatch\Pdk\Common\PdkInterface;
use DeliveryMatch\Pdk\Common\Pdk;
use DI\ContainerBuilder;

class PdkFactory
{
    public static function create(): PdkInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $container = $builder->build();

        $pdk = new Pdk($container);

        Facade::setPdkInstance($pdk);

        return $pdk;
    }
}
