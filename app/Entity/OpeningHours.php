<?php

namespace App\Entity;

use JsonSerializable;

class OpeningHours implements JsonSerializable
{
    /**
     * @param array<int, Hours> $hours
     */
    public function __construct(
        public int $from,
        public int $to,
        public array $hours,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'hours' => join(',', array_map(fn(Hours $hours) => $hours->jsonSerialize(), $this->hours)),
        ];
    }
}
