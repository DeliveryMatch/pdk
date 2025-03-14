<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

final class Rates {
    /**
     * @param int $shipmentId 
     * @param ShippingOption[] $shippingOptions 
     * @return void 
     */
    public function __construct(
        public readonly int $shipmentId,
        public readonly array $shippingOptions
    )
    {
        
    }
}
