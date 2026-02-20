<?php

namespace Melkmeshi\GoTools\Tools;

use Melkmeshi\GoTools\BinaryRunner;
use Melkmeshi\GoTools\DTOs\CompareResult;
use Melkmeshi\GoTools\Exceptions\InvalidInputException;

class DbCompareTool
{
    public function __construct(
        protected BinaryRunner $runner,
    ) {}

    /**
     * Compare IDs between two database sources using raw config arrays.
     *
     * @param  array<string, mixed>  $configA  Laravel-style database config array
     * @param  string  $queryA  SQL query returning a single ID column
     * @param  array<string, mixed>  $configB  Laravel-style database config array
     * @param  string  $queryB  SQL query returning a single ID column
     * @param  string|null  $outputDir  Directory for CSV output files
     */
    public function compare(array $configA, string $queryA, array $configB, string $queryB, ?string $outputDir = null): CompareResult
    {
        $outputDir ??= sys_get_temp_dir() . '/db-compare-' . uniqid();

        $result = $this->runner->run(['db', 'compare'], [
            'source_a' => [
                'driver' => $this->normalizeDriver($configA['driver'] ?? ''),
                'dsn' => $this->buildDsn($configA),
                'query' => $queryA,
            ],
            'source_b' => [
                'driver' => $this->normalizeDriver($configB['driver'] ?? ''),
                'dsn' => $this->buildDsn($configB),
                'query' => $queryB,
            ],
            'output_dir' => $outputDir,
        ], config('go-tools.db_compare_timeout', 300));

        return CompareResult::fromArray($result);
    }

    /**
     * Compare IDs between two named Laravel database connections.
     *
     * @param  string  $connNameA  Laravel connection name (e.g. 'mysql')
     * @param  string  $queryA  SQL query returning a single ID column
     * @param  string  $connNameB  Laravel connection name (e.g. 'odoo_pgsql')
     * @param  string  $queryB  SQL query returning a single ID column
     * @param  string|null  $outputDir  Directory for CSV output files
     */
    public function compareConnections(string $connNameA, string $queryA, string $connNameB, string $queryB, ?string $outputDir = null): CompareResult
    {
        $configA = config("database.connections.{$connNameA}");
        $configB = config("database.connections.{$connNameB}");

        if (! $configA) {
            throw new InvalidInputException("Database connection '{$connNameA}' not found.");
        }
        if (! $configB) {
            throw new InvalidInputException("Database connection '{$connNameB}' not found.");
        }

        return $this->compare($configA, $queryA, $configB, $queryB, $outputDir);
    }

    protected function normalizeDriver(string $driver): string
    {
        return match ($driver) {
            'pgsql' => 'postgres',
            'mariadb' => 'mysql',
            default => $driver,
        };
    }

    protected function buildDsn(array $config): string
    {
        $driver = $this->normalizeDriver($config['driver'] ?? '');

        return match ($driver) {
            'mysql' => $this->buildMysqlDsn($config),
            'postgres' => $this->buildPostgresDsn($config),
            default => throw new InvalidInputException("Unsupported database driver: {$config['driver']}"),
        };
    }

    protected function buildMysqlDsn(array $config): string
    {
        $user = $config['username'] ?? 'root';
        $pass = $config['password'] ?? '';
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';

        return sprintf(
            '%s:%s@tcp(%s:%s)/%s?charset=%s&parseTime=true',
            $user,
            $pass,
            $host,
            $port,
            $database,
            $charset,
        );
    }

    /**
     * Map Laravel/libpq sslmode values to Go lib/pq supported values.
     * Go's lib/pq supports: disable, require, verify-full, verify-ca.
     */
    protected function normalizePgSslMode(string $mode): string
    {
        return match ($mode) {
            'prefer', 'allow' => 'require',
            default => $mode,
        };
    }

    protected function buildPostgresDsn(array $config): string
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 5432;
        $user = $config['username'] ?? 'postgres';
        $pass = $config['password'] ?? '';
        $database = $config['database'] ?? '';
        $sslmode = $this->normalizePgSslMode($config['sslmode'] ?? 'disable');

        return sprintf(
            'host=%s port=%s user=%s password=%s dbname=%s sslmode=%s',
            $host,
            $port,
            $user,
            $pass,
            $database,
            $sslmode,
        );
    }
}
