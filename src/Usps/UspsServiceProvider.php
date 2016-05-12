<?php 
namespace Usps;

use Illuminate\Support\ServiceProvider;

class UspsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['usps'] = $this->app->share(function($app)
        {
            $config = \Config::get('services.usps');
            return new Usps($config);
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('usps');
    }

}