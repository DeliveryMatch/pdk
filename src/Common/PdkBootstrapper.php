<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Factory\PdkFactory;
use DeliveryMatch\Sdk\HttpClient\ApiEnvironment;

class PdkBootstrapper implements PdkBootstrapperInterface
{
    protected static bool $isInitialized = false;
    protected static PdkInterface $pdk;

    public function __construct()
    {
    }

    final public static function setup(int $clientId, string $apikey, ApiEnvironment $environment): PdkInterface
    {
        if (self::$isInitialized) {
            return self::$pdk;
        }

        self::$pdk = (new static())->createInstance($clientId, $apikey, $environment);
        self::$isInitialized = true;

        return self::$pdk;
    }

    final public static function uninitialize(): void
    {
        self::$isInitialized = false;
    }


    protected function createInstance(int $clientId, string $apikey, ApiEnvironment $environment): PdkInterface
    {
        return PdkFactory::create($clientId, $apikey, $environment, $this->getAdditionalConfiguration());
    }

    protected function getAdditionalConfiguration(): array
    {
        return [];
    }
}
