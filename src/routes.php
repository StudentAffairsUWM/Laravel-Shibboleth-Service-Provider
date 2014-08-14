<?php

/**
 * Login route, directs to the Shibboleth SP, which directs
 * to the IDP.
 */
Route::get('/login', 'Saitswebuwm\Shibboleth\ShibbolethController@create');

/**
 * Logout route, directs to the Shibboleth SP, which logs out the user.
 */
Route::get('/logout',  'Saitswebuwm\Shibboleth\ShibbolethController@destroy');

/**
 * This route gives current session information. It can be useful for
 * debugging issues.
 */
Route::get('/session', 'Saitswebuwm\Shibboleth\ShibbolethController@session');

/**
 * This route allows for returning from shibboleth.
 */
Route::get('/idp', 'Saitswebuwm\Shibboleth\ShibbolethController@idpAuthorize');

/**
 * Local login, authentication done against database password field
 */
Route::get('/local', 'Saitswebuwm\Shibboleth\ShibbolethController@localCreate');

Route::post('/local', 'Saitswebuwm\Shibboleth\ShibbolethController@localAuthorize');

/**
 * Default views on successful auth
 */

Route::get('/idp_landing', 'Saitswebuwm\Shibboleth\ShibbolethController@idp_landing');


Route::get('/local_landing', 'Saitswebuwm\Shibboleth\ShibbolethController@local_landing');

/**
 * Landing for unsuccessful auth
 */

Route::get('/unauthorized', function()
{
   return View::make(\Config::get('shibboleth::shibboleth.default_unauth'));
});

Route::get('emulated/idp', 'Saitswebuwm\Shibboleth\ShibbolethController@emulateIdp');
Route::post('emulated/idp', 'Saitswebuwm\Shibboleth\ShibbolethController@emulateIdp');
Route::get('emulated/login', 'Saitswebuwm\Shibboleth\ShibbolethController@emulateLogin');
Route::get('emulated/logout', 'Saitswebuwm\Shibboleth\ShibbolethController@emulateLogout');