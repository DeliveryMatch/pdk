<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DateTimeImmutable;
use DeliveryMatch\Pdk\Factory\RateFactory;
use DeliveryMatch\Pdk\Model\Address;
use DeliveryMatch\Pdk\Model\Carrier;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\DropoffDeliveryOption;
use DeliveryMatch\Pdk\Model\OpeningHour;
use DeliveryMatch\Pdk\Model\OpeningHours;
use DeliveryMatch\Pdk\Model\PickupShippingOption;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\Price;
use DeliveryMatch\Pdk\Model\Rates;
use DeliveryMatch\Pdk\Model\ServiceLevel;
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
    public function __construct(protected Container $container, protected Cache $cache)
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
        $format = "Y-m-d H:i";

        $api = $this->api();

        if ($request->hasIdentifier()) {
            $response = $api->shipments()->update($request);
        } else {
            $response = $api->shipments()->insert($request);
        }

        // foreach ($response["dropoffMethods"]["all"] as $date) {
        //     foreach ($date as $method) {
        //         $pickupDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['pickupTime']}");
        //         $cutoffDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['cutoffTime']}");

        //         if ($pickupDate === false || $cutoffDate === false) {
        //             continue;
        //         }

        //         if (isset($method['dateDelivery'])) {
        //             $deliveryDateFrom = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeFrom']}");
        //             $deliveryDateTo = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeTo']}");
        //         } else {
        //             $deliveryDateFrom = false;
        //             $deliveryDateTo = false;
        //         }

        //         $shippingOptions[] = new DropoffDeliveryOption(
        //             methodId: $method['methodID'] ?? $method["methodId"],
        //             checkId: $method['checkID'] ?? $method["checkId"],
        //             carrier: new Carrier(
        //                 id: $method['carrier']['id'],
        //                 name: $method['carrier']['name'],
        //                 code: $method['carrier']['code'],
        //             ),
        //             serviceLevel: new ServiceLevel(
        //                 id: $method['serviceLevel']['id'],
        //                 name: $method['serviceLevel']['name'],
        //                 description: $method['serviceLevel']['description'],
        //             ),
        //             configurationId: $method['configurationID'],
        //             tariffId: $method['tariffID'],
        //             routeId: $method['routeID'],
        //             price: new Price(
        //                 buy: $method['buy_price'],
        //                 sell: $method['price'],
        //                 currency: $method['currency']
        //             ),
        //             description: $method['serviceLevel']['description'],
        //             title: $method['name'],
        //             pickupWindow: new PickupWindow(
        //                 pickupTime: $pickupDate,
        //                 cutoffTime: $cutoffDate
        //             ),
        //             deliveryWindow: $deliveryDateTo && $deliveryDateFrom
        //                 ? new DeliveryWindow(
        //                     from: $deliveryDateFrom,
        //                     to: $deliveryDateTo,
        //                 ) : null,
        //             address: new Address(
        //                 street: $method["address"]["street"],
        //                 houseNumber: $method["address"]["number"],
        //                 city: $method["address"]["city"],
        //                 country: $method["address"]["country"],
        //                 postcode: $method["address"]["postcode"],
        //                 latitude: $method["latitude"],
        //                 longitude: $method["longitude"],
        //             ),
        //             openingHours: new OpeningHours(
        //                 monday: new OpeningHour(from: $method["openinghours"]["1"]["from"], to: $method["openinghours"]["1"]["to"]),
        //                 tuesday: new OpeningHour(from: $method["openinghours"]["2"]["from"], to: $method["openinghours"]["2"]["to"]),
        //                 wednesday: new OpeningHour(from: $method["openinghours"]["3"]["from"], to: $method["openinghours"]["3"]["to"]),
        //                 thursday: new OpeningHour(from: $method["openinghours"]["4"]["from"], to: $method["openinghours"]["4"]["to"]),
        //                 friday: new OpeningHour(from: $method["openinghours"]["5"]["from"], to: $method["openinghours"]["5"]["to"]),
        //                 saturday: new OpeningHour(from: $method["openinghours"]["6"]["from"], to: $method["openinghours"]["6"]["to"]),
        //                 sunday: new OpeningHour(from: $method["openinghours"]["7"]["from"], to: $method["openinghours"]["7"]["to"]),
        //             )
        //         );
        //     }
        // }

        // foreach ($response["pickupMethods"]["all"] as $date) {
        //     foreach ($date as $method) {
        //         $pickupDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['pickupTime']}");
        //         $cutoffDate = DateTimeImmutable::createFromFormat($format, "{$method['datePickup']} {$method['cutoffTime']}");

        //         if ($pickupDate === false || $cutoffDate === false) {
        //             continue;
        //         }

        //         if (isset($method['dateDelivery'])) {
        //             $deliveryDateFrom = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeFrom']}");
        //             $deliveryDateTo = DateTimeImmutable::createFromFormat($format, "{$method['dateDelivery']} {$method['timeTo']}");
        //         } else {
        //             $deliveryDateFrom = false;
        //             $deliveryDateTo = false;
        //         }

        //         $shippingOptions[] = new PickupShippingOption(
        //             methodId: $method['methodID'] ?? $method["methodId"],
        //             checkId: $method['checkID'] ?? $method["checkId"],
        //             carrier: new Carrier(
        //                 id: $method['carrier']['id'],
        //                 name: $method['carrier']['name'],
        //                 code: $method['carrier']['code'],
        //             ),
        //             serviceLevel: new ServiceLevel(
        //                 id: $method['serviceLevel']['id'],
        //                 name: $method['serviceLevel']['name'],
        //                 description: $method['serviceLevel']['description'],
        //             ),
        //             configurationId: $method['configurationID'],
        //             tariffId: $method['tariffID'],
        //             routeId: $method['routeID'],
        //             price: new Price(
        //                 buy: $method['price_buy'],
        //                 sell: $method['price_sell'],
        //                 currency: $method['currency']
        //             ),
        //             description: $method['description'],
        //             title: $method['name'],
        //             pickupWindow: new PickupWindow(
        //                 pickupTime: $pickupDate,
        //                 cutoffTime: $cutoffDate
        //             ),
        //             deliveryWindow: $deliveryDateTo && $deliveryDateFrom
        //                 ? new DeliveryWindow(
        //                     from: $deliveryDateFrom,
        //                     to: $deliveryDateTo,
        //                 ) : null,
        //             address: new Address(
        //                 street: $method["address"]["street"],
        //                 houseNumber: $method["address"]["number"],
        //                 city: $method["address"]["city"],
        //                 country: $method["address"]["country"],
        //                 postcode: $method["address"]["postcode"],
        //                 latitude: $method["latitude"],
        //                 longitude: $method["longitude"],
        //             ),
        //             openingHours: new OpeningHours(
        //                 monday: new OpeningHour(from: $method["openinghours"]["1"]["from"], to: $method["openinghours"]["1"]["to"]),
        //                 tuesday: new OpeningHour(from: $method["openinghours"]["2"]["from"], to: $method["openinghours"]["2"]["to"]),
        //                 wednesday: new OpeningHour(from: $method["openinghours"]["3"]["from"], to: $method["openinghours"]["3"]["to"]),
        //                 thursday: new OpeningHour(from: $method["openinghours"]["4"]["from"], to: $method["openinghours"]["4"]["to"]),
        //                 friday: new OpeningHour(from: $method["openinghours"]["5"]["from"], to: $method["openinghours"]["5"]["to"]),
        //                 saturday: new OpeningHour(from: $method["openinghours"]["6"]["from"], to: $method["openinghours"]["6"]["to"]),
        //                 sunday: new OpeningHour(from: $method["openinghours"]["7"]["from"], to: $method["openinghours"]["7"]["to"]),
        //             )
        //         );
        //     }
        // }

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
