<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

use DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest;

interface PdkInterface
{
    public function get(string $key): mixed;
    public function has(string $key): mixed;
    public function checkConnection(): bool;
    public function fetchShippingOptions(ShipmentRequest $request): array;
}
