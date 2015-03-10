Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for Laravel 5.

** Please Note **

This is still a work in progress. Do not use in production!

** Seriously, read that note! **

**Please Note**
In order to use this plugin, we assume you already have a pre-existing Shibboleth SP and Shibboleth IdP configured. This does not go into explaining how to set that up. I recommend [http://www.google.com/](http://www.google.com/ "Google"), they've got some pretty cool things.

We also recommend that you use the *tagged* versions as they are proven to work, and are also **stable**. We will likely remove all old tags once v1.0.0 is released to avoid clutter.

## Installation ##

Include the following in your `composer.json` file and run `composer update` (or `composer install` if it's a new project).


    {
        "require": {
            "saitswebuwm/shibboleth": "dev-master"
        }
    }

Then, you will want to include the following line in the end of your `/app/config/app.php` file in the `Providers` array.

    'Saitswebuwm\Shibboleth\ShibbolethServiceProvider'

Add this to your config/auth.php

    /*
    |--------------------------------------------------------------------------
    | Group Model
    | --------------------------------------------------------------------------
    |
    | When using the "shibboleth" authentication driver, it requires that a
    | group model is supported. Of course, it is often just the "Group" model
    | but you may use whatever you like.
    |
    */
    
    'group_model' => 'App\Group',

This includes migrations for both a User and Group table, and will create the models for you. ADD MORE HERE?

Now all your file changes are (mostly) ready. Run the following two commands to create the needed database tables and configuration files.

    $ php artisan vendor:publish
    $ php artisan migrate

Once this is done, you can activate the Shibboleth driver in your `/config/auth.php` file.

    'driver' => 'shibboleth'

You will need to configure your `.htaccess` or other web server configurations with whatever your setup involves. By default, we have included a `.htaccess` in the `/src/` directory that will allow for both Shibboleth and non-Shibboleth users to view the application.

## Recent Changes ##

### v0.5.5 ###

- Added in the ability to *emulate* a Shibboleth IdP environment with the help of [https://github.com/mrclay/shibalike](https://github.com/mrclay/shibalike "Shibalike")
