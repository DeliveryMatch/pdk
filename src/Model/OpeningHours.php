<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class OpeningHours
{
    public function __construct(
        public readonly OpeningHour $monday,
        public readonly OpeningHour $tuesday,
        public readonly OpeningHour $wednesday,
        public readonly OpeningHour $thursday,
        public readonly OpeningHour $friday,
        public readonly OpeningHour $saturday,
        public readonly OpeningHour $sunday,
    ) {

    }
}
