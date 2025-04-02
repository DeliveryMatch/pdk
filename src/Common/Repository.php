<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Model\ShippingOption;

interface Repository
{
    public function setShipmentId(int $shipmentId): void;
    public function getShipmentId(): ?int;

    /**
     * @param ShippingOption[] $shippingOptions
     * @return void
     */
    public function setShippingOptions(array $shippingOptions): void;

    /**
     * @return ShippingOption[]
     */
    public function getShippingOptions(): array;

    public function setCheckId(string $checkId): void;
    public function getCheckId(): ?string;

    public function flush(): void;
}
