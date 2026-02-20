<?php

namespace Melkmeshi\GoTools\Console;

use Illuminate\Console\Command;
use Melkmeshi\GoTools\BinaryInstaller;
use Melkmeshi\GoTools\BinaryResolver;

class InstallCommand extends Command
{
    protected $signature = 'go-tools:install
                            {--ver= : Specific version to install (e.g. v1.0.0)}
                            {--force : Force reinstall even if already installed}';

    protected $description = 'Download and install the Go tools binary for your platform';

    public function handle(BinaryInstaller $installer, BinaryResolver $resolver): int
    {
        $version = $this->option('ver');
        $force = $this->option('force');

        $this->info('Go Tools Binary Installer');
        $this->newLine();

        $platform = $resolver->platform();
        $this->line("  Platform: <comment>{$platform}</comment>");

        if (! $force && $installer->isInstalled()) {
            $currentVersion = $installer->installedVersion();
            $this->line("  Current version: <comment>{$currentVersion}</comment>");

            if (! $version || $version === $currentVersion) {
                $this->info('Binary is already installed and up to date.');

                return self::SUCCESS;
            }
        }

        $targetVersion = $version ?? config('go-tools.version', 'latest');
        $this->line("  Installing version: <comment>{$targetVersion}</comment>");
        $this->newLine();

        try {
            $path = $installer->install($version);
            $this->info("Binary installed successfully at: {$path}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Installation failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
