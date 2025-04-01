<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Model\Carrier;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\HomeDeliveryOption;
use DeliveryMatch\Pdk\Model\Price;
use DeliveryMatch\Pdk\Model\ServiceLevel;
use DeliveryMatch\Pdk\Model\ShippingOption;

class HomeDeliveryFactory implements ShippingOptionFactory
{
    public static function create(array $method, PickupWindow $pickupWindow, ?DeliveryWindow $deliveryWindow): ShippingOption
    {
        return new HomeDeliveryOption(
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
            pickupWindow: $pickupWindow,
            deliveryWindow: $deliveryWindow
        );
    }
}
