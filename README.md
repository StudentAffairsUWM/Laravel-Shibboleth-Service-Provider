Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for Laravel 5.

## Features ##

- Compatibility with Laravel 5
- Includes User and Group model examples
- Ability to *emulate* an IdP (via [https://github.com/mrclay/shibalike](https://github.com/mrclay/shibalike "Shibalike"))

## Pre-Requisites ##

In order to use this plugin, we assume you already have a pre-existing Shibboleth SP and Shibboleth IdP configured. This does not (and will not) go into explaining how to set that up.

## Installation ##

Include the following in your `composer.json` file and run `composer update` (or `composer install` if it's a new project).

    {
        "require": {
            "studentaffairsuwm/shibboleth": "1.0.0"
        }
    }

Then, append the following line inside your `/config/app.php` file within the `Providers` array.

    'StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider'

You'll also want to add this to your `/config/auth.php` file.

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

Finally, we just need to publish to include some default models, the database migrations, and the configuration file in your project. We include migrations for a simple user and group table, it is up to you to expand upon those.

Run the following commands to publish and then migrate your database:

    $ php artisan vendor:publish
    $ php artisan migrate

Once the migrations have run successfully, change the driver to `shibboleth` in your `/config/auth.php` file.

    'driver' => 'shibboleth'

## Looking for Laravel 4? ##

We have stopped development on the Laravel 4 version of this plugin for now. We are welcoming pull requests, however! Feel free to use any tag below 1.0.0 for Laravel 4 compatible versions.
