<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Facade\Logger;
use DeliveryMatch\Pdk\Factory\RateFactory;
use DeliveryMatch\Pdk\Model\Rates;
use DeliveryMatch\Pdk\Model\ShippingOption;
use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;
use DeliveryMatch\Sdk\Client;
use DeliveryMatch\Sdk\Exception\DeliveryMatchApiException;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Http\Client\Exception;
use JsonException;
use Throwable;

class Pdk implements PdkInterface
{
    private const UNDIFINED_SHIPMENT_STATE = -1;
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
        } catch (DeliveryMatchApiException $e) {
            Logger::warning("Could not connect with DeliveryMatch API. Please check API credentials. Message: {$e->getMessage()}");
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
        $rates = $this->getRates($request);

        $this->cache->setShipmentId($rates->shipmentId);
        $this->cache->setShippingOptions($rates->getShippingOptions());

        return $rates;
    }

    private function getRates(ShipmentRequest $request): Rates
    {
        $api = $this->api();

        try {
            $response = $request->hasIdentifier()
                ? $api->shipments()->update($request)
                : $api->shipments()->insert($request);

            return RateFactory::create($response);
        } catch (DeliveryMatchApiException $e) {
            Logger::error("Could not fetch shipping options from DeliveryMatch. Error: {$e->getMessage()}");

            if (in_array($e->getMessage(), ["Only drafts or new shipments can be updated", "Shipment not found"])) {
                $this->cache->setShipmentId(self::UNDIFINED_SHIPMENT_STATE);
                return $this->getRates($request->copyWithoutId());
            }

            return new Rates($request->shipment->id ?? self::UNDIFINED_SHIPMENT_STATE);
        } catch (Throwable $e) {
            Logger::emergency("Unexpected error while getting rates. {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return new Rates($request->shipment->id ?? self::UNDIFINED_SHIPMENT_STATE);
        }
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
            Logger::warning("No shipping option found when trying to add option to shipment");
            return false;
        }

        try {
            $api->shipments()->selectMethod($shipmentId, $shippingOption->methodId);
        } catch (DeliveryMatchApiException $e) {
            Logger::error("Could not update shipment method in DeliveryMatch. shipment_id=$shipmentId, methodId={$shippingOption->methodId}, message={$e->getMessage()}");
            return false;
        }

        return true;
    }

    public function updateShipmentToNew(int $shipmentId, ?string $orderNumber = null): bool
    {
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
            $this->api()->post("/updateShipment", body: $request);
        } catch (DeliveryMatchApiException $e) {
            Logger::error("Could not update shipment to NEW in DeliveryMatch. shipment_id=$shipmentId, orderNumber={$orderNumber}, message={$e->getMessage()}");
            return false;
        }

        return true;
    }

    public function bookShipment(int $shipmentId): array
    {
        $request = [
            "client" => [
                "id" => $this->get("clientId"),
                "action" => "book",
            ],
            "shipment" => [
                "id" => $shipmentId
            ]
        ];

        try {
            return $this->api()->post("/updateShipment", body: $request);
        } catch (DeliveryMatchApiException $e) {
            Logger::error("Could not book shipment $shipmentId. Error: {$e->getMessage()}");
            throw $e;
        }
    }
}
