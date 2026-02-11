<?php

namespace Melkmeshi\GoTools\Facades;

use Illuminate\Support\Facades\Facade;
use Melkmeshi\GoTools\Tools\SetsTool;

/**
 * @method static array intersect(array $setA, array $setB)
 * @method static array union(array $setA, array $setB)
 * @method static array diff(array $setA, array $setB)
 *
 * @see \Melkmeshi\GoTools\Tools\SetsTool
 */
class Sets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SetsTool::class;
    }
}
