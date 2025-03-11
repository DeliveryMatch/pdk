<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

use DateTimeImmutable;

class PickupWindow
{
    public function __construct(
        public readonly DateTimeImmutable $pickupTime,
        public readonly DateTimeImmutable $cutoffTime,
    ) {

    }
}
