<?php


declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class Price
{
    public function __construct(
        public readonly float $buy,
        public readonly float $sell,
        public readonly string $currency
    ) {

    }
}
