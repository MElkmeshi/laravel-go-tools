<?php

namespace Melkmeshi\GoTools\Facades;

use Illuminate\Support\Facades\Facade;
use Melkmeshi\GoTools\DTOs\CompareResult;
use Melkmeshi\GoTools\Tools\DbCompareTool;

/**
 * @method static CompareResult compare(array $configA, string $queryA, array $configB, string $queryB, ?string $outputDir = null)
 * @method static CompareResult compareConnections(string $connNameA, string $queryA, string $connNameB, string $queryB, ?string $outputDir = null)
 *
 * @see \Melkmeshi\GoTools\Tools\DbCompareTool
 */
class DbCompare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DbCompareTool::class;
    }
}
