<?php

namespace Melkmeshi\GoTools\Tools;

use Melkmeshi\GoTools\BinaryRunner;
use Melkmeshi\GoTools\DTOs\DistanceMatrix;
use Melkmeshi\GoTools\DTOs\Route;
use Melkmeshi\GoTools\Exceptions\InvalidInputException;

class OsrmTool
{
    public function __construct(
        protected BinaryRunner $runner,
    ) {}

    /**
     * Calculate a route between coordinates.
     *
     * @param  array<array{0: float, 1: float}>  $coordinates  Array of [lat, lng] pairs
     */
    public function route(array $coordinates): Route
    {
        if (count($coordinates) < 2) {
            throw new InvalidInputException('At least 2 coordinates are required.');
        }

        $result = $this->runner->run(['osrm', 'route'], [
            'coordinates' => $coordinates,
            'server_url' => config('go-tools.osrm_url'),
        ]);

        return Route::fromArray($result);
    }

    /**
     * Calculate distance/duration matrix between origins and destinations.
     *
     * @param  array<array{0: float, 1: float}>  $origins
     * @param  array<array{0: float, 1: float}>  $destinations
     */
    public function table(array $origins, array $destinations): DistanceMatrix
    {
        if (empty($origins) || empty($destinations)) {
            throw new InvalidInputException('Origins and destinations are required.');
        }

        $result = $this->runner->run(['osrm', 'table'], [
            'origins' => $origins,
            'destinations' => $destinations,
            'server_url' => config('go-tools.osrm_url'),
        ]);

        return DistanceMatrix::fromArray($result);
    }
}
