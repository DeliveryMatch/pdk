<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\ShippingOption;

interface ShippingOptionFactory
{
    public static function create(array $method, PickupWindow $pickupWindow, ?DeliveryWindow $deliveryWindow): ShippingOption;
}
