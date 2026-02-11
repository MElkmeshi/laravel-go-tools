<?php

namespace Melkmeshi\GoTools\Console;

use Illuminate\Console\Command;
use Melkmeshi\GoTools\BinaryInstaller;
use Melkmeshi\GoTools\BinaryResolver;

class StatusCommand extends Command
{
    protected $signature = 'go-tools:status';

    protected $description = 'Show the status of the Go tools binary';

    public function handle(BinaryInstaller $installer, BinaryResolver $resolver): int
    {
        $this->info('Go Tools Status');
        $this->newLine();

        $platform = $resolver->platform();
        $installed = $installer->isInstalled();
        $version = $installed ? $installer->installedVersion() : null;

        $this->table(['Property', 'Value'], [
            ['Platform', $platform],
            ['Installed', $installed ? 'Yes' : 'No'],
            ['Version', $version ?? 'N/A'],
            ['Binary path', $installed ? $resolver->resolve() : 'Not found'],
            ['Config version', config('go-tools.version', 'latest')],
            ['OSRM URL', config('go-tools.osrm_url')],
        ]);

        if (! $installed) {
            $this->warn('Binary not installed. Run: php artisan go-tools:install');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
