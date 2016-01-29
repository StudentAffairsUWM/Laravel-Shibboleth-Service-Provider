<?php

// Login Route (Shibboleth)
Route::get('/login', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@create');
// Logout Route (Shibboleth and Local)
Route::get('/logout', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@destroy');
// Shibboleth IdP Callback
Route::get('/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@idpAuthorize');

// Login Route (Local)
Route::get('/local', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@localCreate');
// Login Callback (Local)
Route::post('/local', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@localAuthorize');

// Login Callback (Emulated)
Route::get('emulated/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
// Login Callback (Emulated)
Route::post('emulated/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
// Login Route (Emulated)
Route::get('emulated/login', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogin');
// Logout Route (Emulated)
Route::get('emulated/logout', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogout');
