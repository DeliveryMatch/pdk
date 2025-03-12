<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Factory\PdkFactory;

class PdkBootstrapper implements PdkBootstrapperInterface
{
    protected static bool $isInitialized = false;
    protected static PdkInterface $pdk;

    public function __construct()
    {
    }

    final public static function setup(int $clientId, string $apikey, array ...$configs): PdkInterface
    {
        if (self::$isInitialized) {
            return self::$pdk;
        }

        self::$pdk = (new static())->createInstance($clientId, $apikey, ...$configs);
        self::$isInitialized = true;

        return self::$pdk;
    }

    protected function createInstance(int $clientId, string $apikey): PdkInterface
    {
        return PdkFactory::create($clientId, $apikey, ...$this->getAdditionalConfiguration());
    }

    protected function getAdditionalConfiguration(): array
    {
        return [];
    }
}
