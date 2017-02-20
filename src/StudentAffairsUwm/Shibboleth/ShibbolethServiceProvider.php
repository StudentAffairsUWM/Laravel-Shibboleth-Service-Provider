<?php namespace StudentAffairsUwm\Shibboleth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;

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
        $this->app->register('Tymon\JWTAuth\Providers\JWTAuthServiceProvider');
        $loader = AliasLoader::getInstance();
        $loader->alias('JWTAuth', 'Tymon\JWTAuth\Facades\JWTAuth');
        $loader->alias('JWTFactory', 'Tymon\JWTAuth\Facades\JWTFactory');

        $this->app['auth']->provider('shibboleth', function ($app) {
            return new Providers\ShibbolethUserProvider($app['config']['auth.providers.users.model']);
        });

        // Publish the configuration, migrations, and User / Group models
        $this->publishes([
            __DIR__ . '/../../config/shibboleth.php' => config_path('shibboleth.php'),
            __DIR__ . '/../../database/migrations/'  => base_path('/database/migrations'),
            __DIR__ . '/../../resources/views/'  => base_path('/resources/views'),
            __DIR__ . '/User.php'                    => base_path('/app/User.php'),
            __DIR__ . '/Group.php'                   => base_path('/app/Group.php'),
        ]);

        Route::group(['middleware' => 'web'], function () {
            require __DIR__ . '/../../routes.php';
        });
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
