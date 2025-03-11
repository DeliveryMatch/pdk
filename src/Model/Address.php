<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $houseNumber,
        public readonly string $city,
        public readonly string $postcode,
        public readonly string $country,
        public readonly float $latitude,
        public readonly float $longitude,
    ) {

    }
}
