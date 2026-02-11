<?php

namespace Melkmeshi\GoTools\DTOs;

class H3Cell
{
    public function __construct(
        public readonly string $index,
    ) {}

    public function __toString(): string
    {
        return $this->index;
    }
}
