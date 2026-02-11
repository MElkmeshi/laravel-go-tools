<?php

namespace Melkmeshi\GoTools\DTOs;

class Coordinate
{
    public function __construct(
        public readonly float $lat,
        public readonly float $lng,
    ) {}

    public function toArray(): array
    {
        return [$this->lat, $this->lng];
    }
}
