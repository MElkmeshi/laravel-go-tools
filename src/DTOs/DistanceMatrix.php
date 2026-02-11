<?php

namespace Melkmeshi\GoTools\DTOs;

class DistanceMatrix
{
    public function __construct(
        public readonly array $durations,
        public readonly array $distances,
        public readonly array $sources,
        public readonly array $destinations,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            durations: $data['durations'] ?? [],
            distances: $data['distances'] ?? [],
            sources: $data['sources'] ?? [],
            destinations: $data['destinations'] ?? [],
        );
    }
}
