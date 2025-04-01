<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class DropoffDeliveryOption extends ShippingOption
{
    public function __construct(
        string $methodId,
        string $checkId,
        Carrier $carrier,
        ServiceLevel $serviceLevel,
        int $configurationId,
        int $tariffId,
        int $routeId,
        Price $price,
        string $description,
        string $title,
        PickupWindow $pickupWindow,
        public readonly OpeningHours $openingHours,
        public readonly Address $address,
        ?DeliveryWindow $deliveryWindow = null,
    ) {
        parent::__construct(
            $methodId,
            $checkId,
            $carrier,
            $serviceLevel,
            $configurationId,
            $tariffId,
            $routeId,
            $price,
            $description,
            $title,
            $pickupWindow,
            $deliveryWindow
        );
    }
}
