<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
     */

    'local_login'              => 'localLogin',
    'local_logout'             => 'localLogout',
    'local_authorized'         => 'authed',
    'local_unauthorized'       => 'loginUnauthorized',

    'idp_login'                => '/Shibboleth.sso/Login',
    'idp_logout'               => '/Shibboleth.sso/Logout',
    'shibboleth_authenticated' => 'authed',
    'shibboleth_unauthorized'  => 'loginUnauthorized',

    /*
    |--------------------------------------------------------------------------
    | Emulate an IdP
    |--------------------------------------------------------------------------
    |
    | In case you do not have access to your Shibboleth environment on
    | homestead or your own Vagrant box, you can emulate a Shibboleth
    | environment with the help of Shibalike.
    |
    | Do not use this in production for literally any reason.
    |
     */

    'emulate_idp'              => true,
    'emulate_idp_users'        => array(
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
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'local_login_user_field'   => 'local_email',
    'local_login_pass_field'   => 'local_password',
    'idp_login_email'          => 'mail',
    'idp_login_first'          => 'givenName',
    'idp_login_last'           => 'sn',

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users'            => true, // Should new users be added automatically if they do not exist?
    'shibboleth_group'         => '1', // What group should the new users be automatically added to?

);
