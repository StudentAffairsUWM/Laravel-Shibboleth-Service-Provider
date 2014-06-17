Laravel Shibboleth Service Provider
===================================

Shibboleth Authentication for Laravel

It is suggested to always use the version tagged as they are proven to work and stable. We will likely remove all old tags onces 1.0.0 is released to avoid clutter.

Include the follwoing in your composer.json file and run composer update or install if it's a new project.

<pre><code>{
    "require": {
        "saitswebuwm/shibboleth": "dev-master"
    }
}</code></pre>

Include the following line to the end of your /app/config/app.php  'providers array'

<pre><code>'Saitswebuwm\Shibboleth\ShibbolethServiceProvider'</code></pre>

Add the following array to your User Model.

<pre><code>protected $fillable = array('email', 'first_name', 'last_name', 'password', 'type');</code></pre>

Now we can set it up for your install. Run the following two commands to created the needed database tables and config file.

<pre><code>php artisan config:publish saitswebuwm/shibboleth
php artisan migrate --package="saitswebuwm/shibboleth"
php artisan view:publish saitswebuwm/shibboleth</code></pre>

Change the following line in your /config/auth.php file to use the the shibboleth driver.

<pre><code>'driver' => 'shibboleth'</code></pre>

You will need to configure your .htaccess with whatever your setup involves. By default I have included a .htaccess in the src directory that will allow both shibboleth and non shibboleth users to view the application. Place it in your public folder if this behavior will work for your application.

For more info on the setup see the wiki section of this repository.
