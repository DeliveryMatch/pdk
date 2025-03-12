<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;
use DeliveryMatch\Sdk\Client;
use DI\Container;
use Http\Client\Exception;
use JsonException;

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

    public function api(): Client
    {
        return $this->container->get("api");
    }

    public function checkConnection(): bool
    {
        $api = $this->api();

        $response = $api->me()->isAuthenticated();

        return true;
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function fetchShippingOptions(ShipmentRequest $request): array
    {
        $api = $this->api();

        if ($request->hasIdentifier()) {
            $response = $api->shipments()->update($request);
        } else {
            $response = $api->shipments()->insert($request);
        }

        return ["Mooise shipping option"];
    }

}
