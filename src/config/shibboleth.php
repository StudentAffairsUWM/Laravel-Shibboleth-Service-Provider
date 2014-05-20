<?php

return array(

	'package' => 'saitswebuwm/shibboleth',

	/*
	|--------------------------------------------------------------------------
	| Default Authentication Driver
	|--------------------------------------------------------------------------
	|
	| This option controls the authentication driver that will be utilized.
	| This driver manages the retrieval and authentication of the users
	| attempting to get access to protected areas of your application.
	|
	| Supported: "database", "eloquent"
	|
	*/

    'port' => ':443',
    'idp_login' => '/Shibboleth.sso/Login',
    'idp_logout' => '/Shibboleth.sso/Logout',
    'local_logout' => 'shibboleth::local',
    'login_fail' => 'shibboleth::unauthorized',

	/*
	|--------------------------------------------------------------------------
	| Default Views
	|--------------------------------------------------------------------------
	|
	| Default views, to change to the views you made you can change the following
	| lines.
	|
	*/

    'login_view' => 'shibboleth::local', // View that local users should use to login
    'shibboleth_view' => 'shibboleth::authorized', // View shibboleth users see after authenticating
    'default_view' => 'shibboleth::authorized', // View users see after authenticating
    'default_unauth' => 'shibboleth::unauthorized', // View users see when rejected

	/*
	|--------------------------------------------------------------------------
	| Defaults Settings 
	|--------------------------------------------------------------------------
	|
	| Change these setting do the proper values for your idp.
	|
	*/

	'local_login_user_field' => 'local_email', //post field used to get username
	'local_login_pass_field' => 'local_password', //post field used to get password
	'idp_login_email' => 'mail', //idp server variable for email address
	'idp_login_first' => 'givenName', //idp server variable for first name
	'idp_login_last' => 'sn', //idp server variable for last name

	/*
	|--------------------------------------------------------------------------
	| Groups Settings
	|--------------------------------------------------------------------------
	|
	| Change the group setting acording to your database and program requirements.
	|
	*/

	'shibboleth_group' => '1', // Default group ID shibboleth users will be added to

);