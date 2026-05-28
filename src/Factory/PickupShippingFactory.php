<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Model\Address;
use DeliveryMatch\Pdk\Model\Carrier;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\OpeningHour;
use DeliveryMatch\Pdk\Model\OpeningHours;
use DeliveryMatch\Pdk\Model\PickupShippingOption;
use DeliveryMatch\Pdk\Model\Price;
use DeliveryMatch\Pdk\Model\ServiceLevel;
use DeliveryMatch\Pdk\Model\ShippingOption;

class PickupShippingFactory implements ShippingOptionFactory
{
    public static function create(array $method, PickupWindow $pickupWindow, ?DeliveryWindow $deliveryWindow): ShippingOption
    {
        return new PickupShippingOption(
            methodId: $method['methodID'] ?? $method["methodId"],
            checkId: $method['checkID'] ?? $method["checkId"],
            carrier: new Carrier(
                id: $method['carrier']['id'],
                name: $method['carrier']['name'],
                code: $method['carrier']['code'],
            ),
            serviceLevel: new ServiceLevel(
                id: $method['service']['id'],
                name: $method['service']['name'],
                description: $method['service']['description'],
            ),
            configurationId: $method['configurationID'],
            tariffId: $method['tariffID'],
            price: new Price(
                buy: $method['price_buy'],
                sell: $method['price_sell'],
                currency: $method['currency']
            ),
            description: $method['description'],
            title: $method['name'],
            pickupWindow: $pickupWindow,
            openingHours: OpeningHours::fromApiResponse($method["openinghours"]),
            address: new Address(
                street: $method["address"]["street"],
                houseNumber: $method["address"]["number"],
                city: $method["address"]["city"],
                country: $method["address"]["country"],
                postcode: $method["address"]["postcode"],
                latitude: $method["address"]["latitude"],
                longitude: $method["address"]["longitude"],
            ),
            deliveryWindow: $deliveryWindow
        );
    }
}
