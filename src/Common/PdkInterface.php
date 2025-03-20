<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Pdk\Model\Rates;
use DeliveryMatch\Pdk\Model\ShippingOption;
use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;

interface PdkInterface
{
    public function get(string $key): mixed;
    public function has(string $key): mixed;
    public function checkConnection(): bool;
    public function fetchShippingOptions(ShipmentRequest $request): Rates;
    public function findShippingOption(): ?ShippingOption;
    public function setSelectedOption(string $checkId): void;
    public function addShippingOptionToShipment(): bool;
    public function updateShipmentToNew(int $shipmentId, ?string $orderNumber = null): bool;
}
