<?php

declare(strict_types=1);

namespace DeliveryMatch\Pdk\Common;

interface PdkInterface
{
    public function get(string $key): mixed;
    public function has(string $key): mixed;
    public function checkConnection(): bool;
}
