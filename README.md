Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for Laravel 5.4

## Features ##

- Compatibility with Laravel 5.4
- Includes User and Group model examples
- Ability to *emulate* an IdP (via [https://github.com/mrclay/shibalike](https://github.com/mrclay/shibalike "Shibalike"))

## Pre-Requisites ##

In order to use this plugin, we assume you already have a pre-existing Shibboleth SP and Shibboleth IdP configured. This does not (and will not) go into explaining how to set that up.

## Installation ##

Use [composer][1] to require the latest release into your project:

    $ composer require saitswebuwm/shibboleth

Then, append the following line inside your `/config/app.php` file within the `Providers` array.

```php
StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider::class,
```

Publish to include some default models, the database migrations, and the configuration file in your project.
We include migrations for a simple user and group table, it is up to you to expand upon those.

Run the following commands to publish and then migrate your database:

    $ php artisan vendor:publish
    $ php artisan migrate

Once the migrations have run successfully, change the driver to `shibboleth` in your `/config/auth.php` file.

When using the "shibboleth" authentication driver, it requires that a
group model is supported. Of course, it is often just the "Group" model
but you may use whatever you like.


```php
'providers' => [
    'users' => [
        'driver'      => 'shibboleth',
        'model'       => App\User::class,
        'group_model' => App\Group::class,
    ],
],
```

## Looking for Laravel 5.0 or 4? ##

Laravel 5.0 should be compatible up to tag 1.1.1

We have stopped development on the Laravel 4 version of this plugin for now. We are welcoming pull requests, however! Feel free to use any tag below 1.0.0 for Laravel 4 compatible versions.

[1]:https://getcomposer.org/
