<?php

namespace Melkmeshi\GoTools\DTOs;

class Route
{
    public function __construct(
        public readonly float $distance,
        public readonly float $duration,
        public readonly string $geometry,
        public readonly array $waypoints = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $route = $data['routes'][0] ?? [];

        return new self(
            distance: $route['distance'] ?? 0,
            duration: $route['duration'] ?? 0,
            geometry: $route['geometry'] ?? '',
            waypoints: $data['waypoints'] ?? [],
        );
    }
}
