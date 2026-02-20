# Laravel Go Tools

Go-powered high-performance tools for Laravel. Offloads CPU-intensive operations to compiled Go binaries for significant speed improvements over pure PHP.

## Features

- **H3** — Uber's H3 geospatial indexing (lat/lng to cell, k-ring, distance, polyfill)
- **OSRM** — Open Source Routing Machine integration (routing, distance matrices)
- **Sets** — Fast set operations on large collections (intersect, union, diff)
- **DbCompare** — Cross-database table comparison (MySQL & PostgreSQL)

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x

## Installation

```bash
composer require melkmeshi/laravel-go-tools
```

Download the Go binary for your platform:

```bash
php artisan go-tools:install
```

Optionally publish the config:

```bash
php artisan vendor:publish --tag=go-tools-config
```

Check installation status:

```bash
php artisan go-tools:status
```

## Usage

### H3 Geospatial Indexing

```php
use Melkmeshi\GoTools\Facades\H3;

// Convert coordinates to H3 cell
$cell = H3::latLngToCell(37.7749, -122.4194, 10);
echo $cell->index; // e.g. "8a283082800ffff"

// Get neighboring cells within k steps
$neighbors = H3::kRing($cell->index, 1);

// Grid distance between two cells
$cellB = H3::latLngToCell(37.7750, -122.4180, 10);
$distance = H3::gridDistance($cell->index, $cellB->index);

// Fill a polygon with H3 cells
$cells = H3::polygonToCells([
    [37.7749, -122.4194],
    [37.3382, -121.8863],
    [37.9, -122.5],
], 8);
```

### OSRM Routing

```php
use Melkmeshi\GoTools\Facades\Osrm;

// Calculate a route
$route = Osrm::route([
    [37.7749, -122.4194],
    [37.3382, -121.8863],
]);

echo $route->distance; // meters
echo $route->duration; // seconds
echo $route->geometry; // encoded polyline

// Distance/duration matrix
$matrix = Osrm::table(
    origins: [[37.7749, -122.4194], [37.3382, -121.8863]],
    destinations: [[37.7749, -122.4194], [37.3382, -121.8863]],
);

echo $matrix->durations[0][1]; // seconds
echo $matrix->distances[0][1]; // meters
```

### Set Operations

```php
use Melkmeshi\GoTools\Facades\Sets;

Sets::intersect([1, 2, 3, 4], [3, 4, 5, 6]); // [3, 4]
Sets::union([1, 2, 3], [3, 4, 5]);            // [1, 2, 3, 4, 5]
Sets::diff([1, 2, 3, 4], [3, 4, 5, 6]);       // [1, 2]
```

### Database Compare

Compare ID sets across two database connections:

```php
use Melkmeshi\GoTools\Facades\DbCompare;

// Using Laravel connection names
$result = DbCompare::compareConnections(
    'mysql',
    'SELECT id FROM orders',
    'pgsql',
    'SELECT id FROM sale_order',
    storage_path('app/compare-results'),
);

echo $result->countA;     // total IDs in source A
echo $result->countB;     // total IDs in source B
echo $result->onlyACount; // IDs only in A
echo $result->onlyBCount; // IDs only in B
echo $result->bothCount;  // IDs in both
echo $result->onlyAFile;  // CSV file path
echo $result->onlyBFile;  // CSV file path

// Or using raw database config arrays
$result = DbCompare::compare(
    config('database.connections.mysql'),
    'SELECT id FROM orders',
    config('database.connections.pgsql'),
    'SELECT id FROM sale_order',
);
```

## Configuration

Environment variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `GO_TOOLS_BINARY_PATH` | auto-detected | Custom path to the Go binary |
| `OSRM_URL` | `http://router.project-osrm.org` | OSRM server URL |
| `GO_TOOLS_TIMEOUT` | `30` | Default command timeout (seconds) |
| `GO_TOOLS_DB_COMPARE_TIMEOUT` | `300` | DB compare timeout (seconds) |

## Supported Platforms

| Platform | Architecture |
|----------|-------------|
| macOS | Apple Silicon (arm64) |
| Linux | x86_64 (amd64) |

## Development

### Building binaries

```bash
./scripts/build.sh
```

Builds Go binaries for all supported platforms into `dist/`. Requires [zig](https://ziglang.org/) for cross-compilation.

### Creating a release

```bash
./scripts/release.sh v0.2.0
```

Builds all binaries and creates a GitHub release with the assets attached.

## License

MIT
