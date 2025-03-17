<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Facade;

use DeliveryMatch\Pdk\Common\PdkInterface;

/**
 * @method static mixed get(string $key)
 * @method static bool has(string $key)
 * @method static bool checkConnection()
 * @method static \DeliveryMatch\Pdk\Model\Rates fetchShippingOptions(\DeliveryMatch\Sdk\Api\Dto\Request\ShipmentRequest $request)
 * @method static \DeliveryMatch\Pdk\Model\ShippingOption|null getSelectedShippingOption()
 * @method static void setSelectedOption(string $checkId)
 * @see \DeliveryMaytch\Pdk\Common\PdkInterface
 */
final class Pdk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PdkInterface::class;
    }
}
