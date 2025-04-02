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
        Price $price,
        string $description,
        string $title,
        PickupWindow $pickupWindow,
        public readonly OpeningHours $openingHours,
        public readonly Address $address,
        ?int $routeId = null,
        ?DeliveryWindow $deliveryWindow = null,
    ) {
        parent::__construct(
            $methodId,
            $checkId,
            $carrier,
            $serviceLevel,
            $configurationId,
            $tariffId,
            $price,
            $description,
            $title,
            $pickupWindow,
            $routeId,
            $deliveryWindow
        );
    }
}
