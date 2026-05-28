<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Model;

class OpeningHours
{
    public function __construct(
        public readonly ?OpeningHour $monday,
        public readonly ?OpeningHour $tuesday,
        public readonly ?OpeningHour $wednesday,
        public readonly ?OpeningHour $thursday,
        public readonly ?OpeningHour $friday,
        public readonly ?OpeningHour $saturday,
        public readonly ?OpeningHour $sunday,
    ) {

    }

    public static function fromApiResponse(array $openingHours): self
    {
        return new self(
            monday: isset($openingHours['1']) ? new OpeningHour($openingHours['1']['from'], $openingHours['1']['to']) : null,
            tuesday: isset($openingHours['2']) ? new OpeningHour($openingHours['2']['from'], $openingHours['2']['to']) : null,
            wednesday: isset($openingHours['3']) ? new OpeningHour($openingHours['3']['from'], $openingHours['3']['to']) : null,
            thursday: isset($openingHours['4']) ? new OpeningHour($openingHours['4']['from'], $openingHours['4']['to']) : null,
            friday: isset($openingHours['5']) ? new OpeningHour($openingHours['5']['from'], $openingHours['5']['to']) : null,
            saturday: isset($openingHours['6']) ? new OpeningHour($openingHours['6']['from'], $openingHours['6']['to']) : null,
            sunday: isset($openingHours['7']) ? new OpeningHour($openingHours['7']['from'], $openingHours['7']['to']) : null,
        );
    }
}
