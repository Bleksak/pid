<?php

namespace App\Entity;

use JsonSerializable;

class PointOfSale implements JsonSerializable
{
    /**
     * @param array<int, OpeningHours> $openingHours
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $type,
        public string $address,
        public float $lon,
        public float $lat,
        public int $services,
        public int $payMethods,
        public array $openingHours,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
            'lon' => $this->lon,
            'lat' => $this->lat,
            'services' => $this->services,
            'payMethods' => $this->payMethods,
            'openingHours' => $this->openingHours,
        ];
    }
}
