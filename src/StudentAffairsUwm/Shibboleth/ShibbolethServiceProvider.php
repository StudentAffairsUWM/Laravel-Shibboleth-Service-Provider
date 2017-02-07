<?php namespace StudentAffairsUwm\Shibboleth;

use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Providers\JWTAuthServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Foundation\AliasLoader;

class ShibbolethServiceProvider extends ServiceProvider
{
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
        $this->app->register(JWTAuthServiceProvider::class);
        $loader = AliasLoader::getInstance();
        $loader->alias('JWTAuth', JWTAuth::class);
        $loader->alias('JWTFactory', JWTFactory::class);

        $this->app['auth']->provider('shibboleth', function ($app) {
            return new Providers\ShibbolethUserProvider($this->app['config']['auth.model']);
        });

        // Publish the configuration, migrations, and User / Group models
        $this->publishes([
            __DIR__ . '/../../config/shibboleth.php' => config_path('shibboleth.php'),
            __DIR__ . '/../../database/migrations/'  => base_path('/database/migrations'),
            __DIR__ . '/../../resources/views/'  => base_path('/resources/views'),
            __DIR__ . '/User.php'                    => base_path('/app/User.php'),
            __DIR__ . '/Group.php'                   => base_path('/app/Group.php'),
        ]);

        include __DIR__ . '/../../routes.php';
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
