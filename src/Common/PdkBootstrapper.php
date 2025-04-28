<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Factory\PdkFactory;
use DeliveryMatch\Sdk\HttpClient\ApiEnvironment;
use DeliveryMatch\Sdk\HttpClient\Builder;

class PdkBootstrapper implements PdkBootstrapperInterface
{
    protected static bool $isInitialized = false;
    protected static PdkInterface $pdk;

    public function __construct()
    {
    }

    final public static function setup(int $clientId, string $apikey, ApiEnvironment $environment, ?Builder $httpClientBuilder = null): PdkInterface
    {
        if (self::$isInitialized) {
            return self::$pdk;
        }

        self::$pdk = (new static())->createInstance($clientId, $apikey, $environment, $httpClientBuilder);
        self::$isInitialized = true;

        return self::$pdk;
    }

    final public static function uninitialize(): void
    {
        self::$isInitialized = false;
    }


    protected function createInstance(int $clientId, string $apikey, ApiEnvironment $environment, ?Builder $httpClientBuilder = null): PdkInterface
    {
        return PdkFactory::create($clientId, $apikey, $environment, $this->getAdditionalConfiguration(), $httpClientBuilder);
    }

    protected function getAdditionalConfiguration(): array
    {
        return [];
    }
}
