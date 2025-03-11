<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class DropoffDeliveryOption extends ShippingOption
{
    public function __construct(
        public readonly OpeningHours $openingHours,
        public readonly Address $address,
    ) {

    }
}
