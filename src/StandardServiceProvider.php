<?php

namespace Restotech\Standard;

use Illuminate\Support\ServiceProvider;

class StandardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/restotech-standard.php', 'restotech-standard');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerConfiguredRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/restotech-standard.php' => config_path('restotech-standard.php'),
            ], 'restotech-standard-config');
        }
    }

    protected function registerConfiguredRoutes(): void
    {
        $routes = [
            'back_office' => __DIR__ . '/../routes/back-office.php',
            'pos' => __DIR__ . '/../routes/pos.php',
            'api' => __DIR__ . '/../routes/api.php',
        ];

        foreach ($routes as $group => $path) {
            if (! $this->routeGroupEnabled($group)) {
                continue;
            }

            $this->loadRoutesFrom($path);
        }
    }

    protected function routeGroupEnabled(string $group): bool
    {
        return (bool) config("restotech-standard.routes.{$group}.enabled", true);
    }
}
