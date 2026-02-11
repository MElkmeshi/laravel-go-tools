<?php

namespace Melkmeshi\GoTools\Facades;

use Illuminate\Support\Facades\Facade;
use Melkmeshi\GoTools\DTOs\DistanceMatrix;
use Melkmeshi\GoTools\DTOs\Route;
use Melkmeshi\GoTools\Tools\OsrmTool;

/**
 * @method static Route route(array $coordinates)
 * @method static DistanceMatrix table(array $origins, array $destinations)
 *
 * @see \Melkmeshi\GoTools\Tools\OsrmTool
 */
class Osrm extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OsrmTool::class;
    }
}
