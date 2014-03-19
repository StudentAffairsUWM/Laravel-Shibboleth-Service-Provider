<?php namespace Saitswebuwm\Shibboleth;

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Auth\Guard;


class ShibbolethServiceProvider extends AuthServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {   
        $this->package('saitswebuwm/shibboleth', 'saitswebuwm/shibboleth');

        $config = $this->app['config']->get('saitswebuwm/shibboleth::shibboleth');

        $this->app['auth']->extend('shibboleth', function($app) {
            return new Guard(new Providers\ShibbolethUserProvider($this->app['hash'], $this->app['config']['auth.model']), $app['session.store']);
        });

        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
}