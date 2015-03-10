<?php

return array(

    'idp_login'              => '/Shibboleth.sso/Login',
    'idp_logout'             => '/Shibboleth.sso/Logout',
    'local_logout'           => 'shibboleth::local',
    'login_fail'             => 'shibboleth::unauthorized',

    'user_model'             => 'App\User',
    'group_modal'            => 'App\Group',

    /*
    |--------------------------------------------------------------------------
    | IdP Emulation
    |--------------------------------------------------------------------------
    |
    | Allows you to emulate an IdP without setting up a test Shibboleth env.
    | Do NOT use this in production for any reason whatsoever.
    | You have been warned.
    |
    | YOU MUST USE FALSE AND NOT "FALSE" PHP CONVERTS ALL STRINGS EXCEPT "0" TO TRUE.
    |
     */
    'emulate_idp'            => false,
    'emulate_idp_users'      => array(
        'admin' => array(
            'uid'         => 'admin',
            'displayName' => 'Admin User',
            'givenName'   => 'Admin',
            'sn'          => 'User',
            'mail'        => 'admin@uwm.edu',
        ),
        'staff' => array(
            'uid'         => 'staff',
            'displayName' => 'Staff User',
            'givenName'   => 'Staff',
            'sn'          => 'User',
            'mail'        => 'staff@uwm.edu',
        ),
        'user'  => array(
            'uid'         => 'user',
            'displayName' => 'User User',
            'givenName'   => 'User',
            'sn'          => 'User',
            'mail'        => 'user@uwm.edu',
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Default Views
    |--------------------------------------------------------------------------
    |
    | Default views, to change to the views you made you can change the following
    | lines.
    |
     */

    'login_view'             => 'shibboleth::local', // View that local users should use to login
    'shibboleth_view'        => 'shibboleth::authorized', // View shibboleth users see after authenticating
    'default_view'           => 'shibboleth::authorized', // View users see after authenticating
    'default_unauth'         => 'shibboleth::unauthorized', // View users see when rejected

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
    'idp_login_email'        => 'mail', //idp server variable for email address
    'idp_login_first'        => 'givenName', //idp server variable for first name
    'idp_login_last'         => 'sn', //idp server variable for last name

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if/how new users are added
    |
     */

    'add_new_users'          => true, // Whether new shibboleth users should be added
    'shibboleth_group'       => '1', // Default group ID shibboleth users will be added to

);
