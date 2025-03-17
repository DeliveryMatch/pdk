<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Model\ShippingOption;

interface Cache
{
    public function setShipmentId(int $shipmentId, string $key = "dm_shipment_id"): void;
    public function getShipmentId(string $key = "dm_shipment_id"): ?int;

    /**
     * @param ShippingOption[] $shippingOptions
     * @return void
     */
    public function setShippingOptions(array $shippingOptions, string $key = "dm_shipping_options"): void;

    /**
     * @param string $key
     * @return ShippingOption[]
     */
    public function getShippingOptions(string $key = "dm_shipping_options"): array;

    public function setCheckId(string $checkId, string $key = "dm_check_id"): void;
    public function getCheckId(string $key = "dm_check_id"): ?string;

    public function flush(array $keys = ["dm_check_id", "dm_shipping_options", "dm_shipment_id"]): void;
}
