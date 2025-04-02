<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Factory\RateFactory;
use DeliveryMatch\Pdk\Model\Rates;
use DeliveryMatch\Pdk\Model\ShippingOption;
use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;
use DeliveryMatch\Sdk\Api\HttpClient\Message\Json;
use DeliveryMatch\Sdk\Client;
use DeliveryMatch\Sdk\Exception\DeliveryMatchApiException;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Http\Client\Exception;
use JsonException;

class Pdk implements PdkInterface
{
    public function __construct(protected Container $container, protected Repository $cache)
    {
    }

    public function setSelectedOption(string $checkId): void
    {
        $this->cache->setCheckId($checkId);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
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

        try {
            $api->me()->isAuthenticated();
        } catch (DeliveryMatchApiException) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function fetchShippingOptions(ShipmentRequest $request): Rates
    {
        $api = $this->api();

        $response = $request->hasIdentifier()
            ? $api->shipments()->update($request)
            : $api->shipments()->insert($request);

        $rates = RateFactory::create($response);

        $this->cache->setShipmentId($rates->shipmentId);
        $this->cache->setShippingOptions($rates->getShippingOptions());

        return $rates;
    }

    public function findShippingOption(): ?ShippingOption
    {
        $shippingOption = current(array_filter($this->cache->getShippingOptions(), fn (ShippingOption $option) => $option->checkId === $this->cache->getCheckId()));

        if (!$shippingOption) {
            return null;
        }

        return $shippingOption;
    }

    public function addShippingOptionToShipment(): bool
    {
        $api = $this->api();

        $shippingOption = $this->findShippingOption();
        $shipmentId = $this->cache->getShipmentId();

        if ($shippingOption === null || $shipmentId === null) {
            return false;
        }

        try {
            $api->shipments()->selectMethod($shipmentId, $shippingOption->methodId);
        } catch (DeliveryMatchApiException $e) {
            return false;
        }

        return true;
    }

    public function updateShipmentToNew(int $shipmentId, ?string $orderNumber = null): bool
    {
        $httpClient = $this->api()->getHttpClient();

        $request = [
            "client" => [
                "id" => $this->get("clientId")
            ],
            "shipment" => [
                "id" => $shipmentId,
                "status" => 'new'
            ]
        ];

        if ($orderNumber !== null) {
            $request["shipment"]["orderNumber"] = $orderNumber;
        }

        try {
            $httpClient->post("/updateShipment", body: Json::encode($request));
        } catch (DeliveryMatchApiException $e) {
            return false;
        }

        return true;
    }
}
