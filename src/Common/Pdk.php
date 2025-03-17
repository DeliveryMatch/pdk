<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DateTimeImmutable;
use DeliveryMatch\Pdk\Model\Carrier;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\HomeDeliveryOption;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\Price;
use DeliveryMatch\Pdk\Model\Rates;
use DeliveryMatch\Pdk\Model\ServiceLevel;
use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;
use DeliveryMatch\Sdk\Client;
use DeliveryMatch\Sdk\Exception\DeliveryMatchApiException;
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
        $format = "Y-m-d H:i";

        $api = $this->api();
        $cache = $this->cache();

        if ($request->hasIdentifier()) {
            $response = $api->shipments()->update($request);
        } else {
            $response = $api->shipments()->insert($request);
        }

        $shippingOptions = [];
        foreach ($response['shipmentMethods']['all'] as $date) {
            foreach ($date as $method) {
                $pickupDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['pickupTime']}");
                $cutoffDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['cutoffTime']}");

                if ($pickupDate === false || $cutoffDate === false) {
                    continue;
                }

                if (isset($method['dateDelivery'])) {
                    $deliveryDateFrom = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeFrom']}");
                    $deliveryDateTo = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeTo']}");
                } else {
                    $deliveryDateFrom = false;
                    $deliveryDateTo = false;
                }

                $shippingOptions[] = new HomeDeliveryOption(
                    methodId: $method['methodID'],
                    checkId: $method['checkID'],
                    carrier: new Carrier(
                        id: $method['carrier']['id'],
                        name: $method['carrier']['name'],
                        code: $method['carrier']['code'],
                    ),
                    serviceLevel: new ServiceLevel(
                        id: $method['serviceLevel']['id'],
                        name: $method['serviceLevel']['name'],
                        description: $method['serviceLevel']['description'],
                    ),
                    configurationId: $method['configurationID'],
                    tariffId: $method['tariffID'],
                    routeId: $method['routeID'],
                    price: new Price(
                        buy: $method['buy_price'],
                        sell: $method['price'],
                        currency: $method['currency']
                    ),
                    description: $method['description'],
                    title: $method['title'] ?? $method['carrier']['name'],
                    pickupWindow: new PickupWindow(
                        pickupTime: $pickupDate,
                        cutoffTime: $cutoffDate
                    ),
                    deliveryWindow: $deliveryDateTo && $deliveryDateFrom
                        ? new DeliveryWindow(
                            from: $deliveryDateFrom,
                            to: $deliveryDateTo,
                        ) : null
                );
            }
        }

        $shipmentId = is_array($response["shipmentID"]) ? current($response["shipmentID"]) : $response["shipmentID"];

        $cache->setShipmentId($shipmentId);
        $cache->setShippingOptions($shippingOptions);

        return new Rates(
            shipmentId: $shipmentId,
            shippingOptions: $shippingOptions
        );
    }

    public function cache(): Cache
    {
        return $this->get("cache");
    }

}
