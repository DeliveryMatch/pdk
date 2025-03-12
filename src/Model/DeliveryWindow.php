<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

use DateTimeImmutable;

class DeliveryWindow
{
    public function __construct(
        public readonly DateTimeImmutable $from,
        public readonly DateTimeImmutable $to,
    ) {

    }
}
