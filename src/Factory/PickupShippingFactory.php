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
            openingHours: new OpeningHours(
                monday: new OpeningHour(from: $method["openinghours"]["1"]["from"], to: $method["openinghours"]["1"]["to"]),
                tuesday: new OpeningHour(from: $method["openinghours"]["2"]["from"], to: $method["openinghours"]["2"]["to"]),
                wednesday: new OpeningHour(from: $method["openinghours"]["3"]["from"], to: $method["openinghours"]["3"]["to"]),
                thursday: new OpeningHour(from: $method["openinghours"]["4"]["from"], to: $method["openinghours"]["4"]["to"]),
                friday: new OpeningHour(from: $method["openinghours"]["5"]["from"], to: $method["openinghours"]["5"]["to"]),
                saturday: new OpeningHour(from: $method["openinghours"]["6"]["from"], to: $method["openinghours"]["6"]["to"]),
                sunday: new OpeningHour(from: $method["openinghours"]["7"]["from"], to: $method["openinghours"]["7"]["to"]),
            ),
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
