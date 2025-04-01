<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Factory;

use DateTimeImmutable;
use DeliveryMatch\Pdk\Model\DeliveryWindow;
use DeliveryMatch\Pdk\Model\PickupWindow;
use DeliveryMatch\Pdk\Model\Rates;
use ReflectionClass;

class RateFactory
{
    private const DATE_TIME_FORMAT = "Y-m-d H:i";
    private const FACTORIES = [
        "shipmentMethods" => HomeDeliveryFactory::class
    ];

    final public static function create(array $apiResponse): Rates
    {
        $shipmentId = is_array($apiResponse["shipmentID"]) ? current($apiResponse["shipmentID"]) : $apiResponse["shipmentID"];

        $rates = new Rates(shipmentId: $shipmentId);

        foreach (self::FACTORIES as $optionType => $factory) {
            $reflectionFactory = new ReflectionClass($factory);

            if ($reflectionFactory->implementsInterface(ShippingOptionFactory::class)) {
                continue;
            }

            foreach ($apiResponse[$optionType]["all"] as $date) {
                foreach ($date as $method) {

                    $pickupDate = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, "{$method['datePickup']} {$method['pickupTime']}");
                    $cutoffDate = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, "{$method['datePickup']} {$method['cutoffTime']}");

                    if ($pickupDate === false || $cutoffDate === false) {
                        continue;
                    }

                    $deliveryWindow = self::getDeliveryWindow(
                        $method["dateDelivery"] ?? null,
                        $method["timeFrom"] ?? null,
                        $method["timeTo"] ?? null
                    );

                    $option = $factory::create($method, new PickupWindow($pickupDate, $cutoffDate), $deliveryWindow);

                    $rates->addShippingOption($option);
                }
            }
        }

        return $rates;
    }

    private static function getDeliveryWindow(?string $dateDelivery, ?string $timeFrom, ?string $timeTo): DeliveryWindow|null
    {
        if (empty($dateDelivery) || empty($timeFrom) || empty($timeTo)) {
            return null;
        }

        $from = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, "{$dateDelivery} {$timeFrom}");
        $to = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, "{$dateDelivery} {$timeTo}");

        if (!$from || !$to) {
            return null;
        }

        return new DeliveryWindow(from: $from, to: $to);
    }
}
