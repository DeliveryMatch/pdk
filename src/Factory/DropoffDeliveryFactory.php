<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Model\Address;
use DeliveryMatch\Pdk\Model\Carrier;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\DropoffDeliveryOption;
use DeliveryMatch\Pdk\Model\OpeningHours;
use DeliveryMatch\Pdk\Model\Price;
use DeliveryMatch\Pdk\Model\ServiceLevel;
use DeliveryMatch\Pdk\Model\ShippingOption;

class DropoffDeliveryFactory implements ShippingOptionFactory
{
    public static function create(array $method, PickupWindow $pickupWindow, ?DeliveryWindow $deliveryWindow): ShippingOption
    {
        return new DropoffDeliveryOption(
            methodId: $method['methodID'] ?? $method["methodId"],
            checkId: $method['checkID'] ?? $method["checkId"],
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
            price: new Price(
                buy: $method['buy_price'],
                sell: $method['price'],
                currency: $method['currency']
            ),
            description: $method['serviceLevel']['description'],
            title: $method['name'],
            pickupWindow: $pickupWindow,
            openingHours: OpeningHours::fromApiResponse($method["openinghours"]),
            address: new Address(
                street: $method["address"]["street"],
                houseNumber: $method["address"]["number"],
                city: $method["address"]["city"],
                country: $method["address"]["country"],
                postcode: $method["address"]["postcode"],
                latitude: $method["latitude"],
                longitude: $method["longitude"],
            ),
            deliveryWindow: $deliveryWindow
        );
    }
}
