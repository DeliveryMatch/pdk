<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

final class Rates
{
    private array $homeDeliveryOptions;
    private array $shippingOptions;
    private array $pickupShippingOptions;
    private array $dropoffDeliveryOptions;

    /**
     * @param int $shipmentId
     * @return void
     */
    public function __construct(
        public readonly int $shipmentId,
    ) {
        $this->homeDeliveryOptions = [];
        $this->shippingOptions = [];
        $this->dropoffDeliveryOptions = [];
        $this->pickupShippingOptions = [];
    }

    public function addShippingOption(ShippingOption $option): void
    {
        array_push($this->shippingOptions, $option);

        switch (true) {
            case $option instanceof PickupShippingOption:
                array_push($this->pickupShippingOptions, $option);
                break;
            case $option instanceof DropoffDeliveryOption:
                array_push($this->dropoffDeliveryOptions, $option);
                break;
            case $option instanceof HomeDeliveryOption:
                array_push($this->homeDeliveryOptions, $option);
                break;
            default:
                break;
        }
    }

    /** @return ShippingOption[]  */
    public function getShippingOptions(): array
    {
        return $this->shippingOptions;
    }

    /** @return HomeDeliveryOption[]  */
    public function getHomeDeliveryOptions(): array
    {
        return $this->homeDeliveryOptions;
    }


    /** @return DropoffDeliveryOption[]  */
    public function getDropoffDeliveryOptions(): array
    {
        return $this->dropoffDeliveryOptions;
    }

    /** @return PickupShippingOption[]  */
    public function getPickupShippingOptions(): array
    {
        return $this->pickupShippingOptions;
    }
}
