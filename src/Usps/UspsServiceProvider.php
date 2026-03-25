<?php

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
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/usps.php', 'usps');

        $this->app->singleton('usps', function () {
            return new Usps(config('usps'));
        });
    }

    public function provides(): array
    {
        return ['usps'];
    }
}
