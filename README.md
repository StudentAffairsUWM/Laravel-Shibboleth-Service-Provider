Laravel-Shibboleth-Service-Provider
===================================

Shibboleth Authentication for Laravel

Include the follwoing in your composer.json file and run composer update or install if it's a new project.

<pre><code>{
    "require": {
        "saitswebuwm/shibboleth": "dev-master": "dev-master"
    }
}</code></pre>

Include the following line to the end of your /app/config/app.php  'providers array'

<pre><code>'Saitswebuwm\Shibboleth\ShibbolethServiceProvider'</code></pre>

You will also want to run the following commands to override config changes outside of the package.

<pre><code>php artisan config:publish saitswebuwm/shibboleth</code></pre>