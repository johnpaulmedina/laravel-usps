<?php

declare(strict_types=1);

/**
 * USPS API v3 — Laravel Service Provider
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Illuminate\Support\ServiceProvider;

class UspsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/usps.php' => config_path('usps.php'),
        ], 'usps-config');

        $this->publishes([
            __DIR__ . '/../../routes/usps.php' => base_path('routes/usps.php'),
        ], 'usps-routes');

        $this->loadRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ValidateAddressCommand::class,
                Commands\TrackPackageCommand::class,
                Commands\ZipLookupCommand::class,
                Commands\PriceCalculatorCommand::class,
                Commands\ServiceStandardsCommand::class,
                Commands\FindLocationsCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/usps.php', 'usps');

        $this->app->singleton('usps', function () {
            return new Usps(config('usps'));
        });
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['usps'];
    }

    protected function loadRoutes(): void
    {
        $publishedRoutes = base_path('routes/usps.php');

        if (file_exists($publishedRoutes)) {
            $this->loadRoutesFrom($publishedRoutes);

            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/../../routes/usps.php');
    }
}
