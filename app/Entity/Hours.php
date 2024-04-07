<?php

namespace App\Entity;

use DateInterval;
use JsonSerializable;

class Hours implements JsonSerializable
{
    public function __construct(
        public DateInterval $from,
        public DateInterval $to,
    ) {}

    public function jsonSerialize(): mixed
    {
        return sprintf("%s-%s", $this->from->format('%H:%I'), $this->to->format('%H:%I'));
    }
}
