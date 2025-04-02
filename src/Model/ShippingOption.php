<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

abstract class ShippingOption
{
    public function __construct(
        public readonly string $methodId,
        public readonly string $checkId,
        public readonly Carrier $carrier,
        public readonly ServiceLevel $serviceLevel,
        public readonly int $configurationId,
        public readonly int $tariffId,
        public readonly Price $price,
        public readonly string $description,
        public readonly string $title,
        public readonly PickupWindow $pickupWindow,
        public readonly ?int $routeId = null,
        public readonly ?DeliveryWindow $deliveryWindow = null,
    ) {

    }
}
