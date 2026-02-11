<?php

namespace Melkmeshi\GoTools;

use Illuminate\Support\ServiceProvider;
use Melkmeshi\GoTools\Console\InstallCommand;
use Melkmeshi\GoTools\Console\StatusCommand;
use Melkmeshi\GoTools\Tools\H3Tool;
use Melkmeshi\GoTools\Tools\OsrmTool;
use Melkmeshi\GoTools\Tools\SetsTool;

class GoToolsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/go-tools.php', 'go-tools');

        $this->app->singleton(BinaryResolver::class);
        $this->app->singleton(BinaryRunner::class);
        $this->app->singleton(H3Tool::class);
        $this->app->singleton(OsrmTool::class);
        $this->app->singleton(SetsTool::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/go-tools.php' => config_path('go-tools.php'),
            ], 'go-tools-config');

            $this->commands([
                InstallCommand::class,
                StatusCommand::class,
            ]);
        }
    }
}
