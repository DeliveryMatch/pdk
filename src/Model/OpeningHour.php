<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class OpeningHour
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
    ) {

    }
}
