<?php

namespace Melkmeshi\GoTools\Facades;

use Illuminate\Support\Facades\Facade;
use Melkmeshi\GoTools\DTOs\H3Cell;
use Melkmeshi\GoTools\Tools\H3Tool;

/**
 * @method static H3Cell latLngToCell(float $lat, float $lng, int $resolution)
 * @method static array kRing(string $index, int $k)
 * @method static int gridDistance(string $origin, string $destination)
 * @method static array polygonToCells(array $polygon, int $resolution)
 *
 * @see \Melkmeshi\GoTools\Tools\H3Tool
 */
class H3 extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return H3Tool::class;
    }
}
