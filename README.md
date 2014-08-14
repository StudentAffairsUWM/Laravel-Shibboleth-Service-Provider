Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for Laravel.

**Please Note**
In order to use this plugin, you need to have a pre-existing  does not explain how to setup your Shibboleth SP or Shibboleth IdP. I recommend [http://www.google.com/](http://www.google.com/ "Google"), they've got some pretty cool things.

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

Also add the following array to your `User` model in `/app/models/User.php`

	protected $fillable = array('email', 'first_name', 'last_name', 'password', 'type');

Now all your file changes are (mostly) ready. Run the following two commands to create the needed database tables and configuration files.

	$ php artisan config:publish saitswebuwm/shibboleth
	$ php artisan migrate --package="saitswebuwm/shibboleth"
	$ php artisan view:publish saitswebuwm/shibboleth

Once this is done, you can activate the Shibboleth driver in your `/app/config/auth.php` file.

	'driver' => 'shibboleth'

You will need to configure your `.htaccess` or other web server configurations with whatever your setup involves. By default, we have included a `.htaccess` in the `/src/` directory that will allow for both Shibboleth and non-Shibboleth users to view the application.

## Recent Changes ##

### v0.5.5 ###

- Added in the ability to *emulate* a Shibboleth IdP environment with the help of [https://github.com/mrclay/shibalike](https://github.com/mrclay/shibalike "Shibalike")
