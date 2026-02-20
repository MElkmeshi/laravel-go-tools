<?php

namespace Melkmeshi\GoTools\DTOs;

class CompareResult
{
    public function __construct(
        public readonly int $countA,
        public readonly int $countB,
        public readonly int $onlyACount,
        public readonly int $onlyBCount,
        public readonly int $bothCount,
        public readonly string $onlyAFile,
        public readonly string $onlyBFile,
        public readonly string $bothFile,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            countA: $data['count_a'] ?? 0,
            countB: $data['count_b'] ?? 0,
            onlyACount: $data['only_a_count'] ?? 0,
            onlyBCount: $data['only_b_count'] ?? 0,
            bothCount: $data['both_count'] ?? 0,
            onlyAFile: $data['only_a_file'] ?? '',
            onlyBFile: $data['only_b_file'] ?? '',
            bothFile: $data['both_file'] ?? '',
        );
    }
}
