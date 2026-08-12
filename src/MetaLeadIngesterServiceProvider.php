<?php

namespace Vendor\MetaLeadIngester;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MetaLeadIngesterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/meta-lead-ingester.php', 'meta-lead-ingester'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/meta-lead-ingester.php' => config_path('meta-lead-ingester.php'),
            ], 'meta-lead-ingester-config');

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'meta-lead-ingester-migrations');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'meta-lead-ingester');

        $this->registerRoutes();
    }

    /**
     * Register package routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        Route::group($this->webRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Get the package route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('meta-lead-ingester.route_prefix', 'api/meta-lead-ingester'),
            'middleware' => ['api'],
        ];
    }

    /**
     * Get the web route group configuration array.
     *
     * @return array
     */
    protected function webRouteConfiguration(): array
    {
        return [
            'prefix' => config('meta-lead-ingester.dashboard_route_prefix', 'meta-lead-ingester/dashboard'),
            'middleware' => config('meta-lead-ingester.dashboard_middleware', ['web']),
        ];
    }
}
