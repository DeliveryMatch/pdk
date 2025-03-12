<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class Carrier
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
    ) {

    }
}
