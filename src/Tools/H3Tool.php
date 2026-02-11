<?php

namespace Melkmeshi\GoTools\Tools;

use Melkmeshi\GoTools\BinaryRunner;
use Melkmeshi\GoTools\DTOs\H3Cell;
use Melkmeshi\GoTools\Exceptions\InvalidInputException;

class H3Tool
{
    public function __construct(
        protected BinaryRunner $runner,
    ) {}

    /**
     * Convert lat/lng to H3 cell index at the given resolution.
     */
    public function latLngToCell(float $lat, float $lng, int $resolution): H3Cell
    {
        if ($resolution < 0 || $resolution > 15) {
            throw new InvalidInputException('Resolution must be between 0 and 15.');
        }

        $result = $this->runner->run(['h3', 'index'], [
            'lat' => $lat,
            'lng' => $lng,
            'resolution' => $resolution,
        ]);

        return new H3Cell($result['index']);
    }

    /**
     * Get k-ring of cells around a given cell.
     *
     * @return array<H3Cell>
     */
    public function kRing(string $index, int $k): array
    {
        $result = $this->runner->run(['h3', 'kring'], [
            'index' => $index,
            'k' => $k,
        ]);

        return array_map(
            fn (string $cell) => new H3Cell($cell),
            $result['cells'] ?? [],
        );
    }

    /**
     * Calculate grid distance between two H3 cells.
     */
    public function gridDistance(string $origin, string $destination): int
    {
        $result = $this->runner->run(['h3', 'distance'], [
            'origin' => $origin,
            'destination' => $destination,
        ]);

        return $result['distance'];
    }

    /**
     * Fill a polygon with H3 cells at the given resolution.
     *
     * @param  array<array{0: float, 1: float}>  $polygon  Array of [lat, lng] coordinates
     * @return array<H3Cell>
     */
    public function polygonToCells(array $polygon, int $resolution): array
    {
        if (count($polygon) < 3) {
            throw new InvalidInputException('Polygon must have at least 3 vertices.');
        }

        $result = $this->runner->run(['h3', 'polyfill'], [
            'polygon' => $polygon,
            'resolution' => $resolution,
        ]);

        return array_map(
            fn (string $cell) => new H3Cell($cell),
            $result['cells'] ?? [],
        );
    }
}
