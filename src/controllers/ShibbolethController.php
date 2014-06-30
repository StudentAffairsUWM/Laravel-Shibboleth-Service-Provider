<?php namespace Saitswebuwm\Shibboleth;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Auth\GenericUser;
use Illuminate\Routing\Controller;

class ShibbolethController extends Controller {

    //Config pathings
    private $cpath = "shibboleth::shibboleth";
    private $ctrpath = "Saitswebuwm\\Shibboleth\\ShibbolethController@";
    
    /**
     * Inject the user into this controller if present.
     */
    public function __construct(GenericUser $user = null)
    {
        $this->user = $user;
    }
    
    /**
     * Create the session, send the user away to the IDP
     * for authentication.
     */
    public function create()
    {
        return Redirect::to( 'https://' . Request::server('SERVER_NAME') . ':' . Request::server('SERVER_PORT') . Config::get("$this->cpath.idp_login") . '?target=' . action($this->ctrpath . "idpAuthorize"));
    }
    
    /**
     * Login for users not using the IDP
     */
    public function localCreate()
    {
        return View::make(Config::get("$this->cpath.login_view"));
    }
    
    public function localAuthorize()
    {
        $email = \Input::get(Config::get("$this->cpath.local_login_user_field"));
        $password = \Input::get(Config::get("$this->cpath.local_login_pass_field"));

        if (Auth::attempt(array('email' => $email, 'password' => $password), true))
        {
            $user = UserShibboleth::where('email', '=', $email)->first();
            if (isset($user->first_name)) Session::put('first', $user->first_name);
            if (isset($user->last_name)) Session::put('last', $user->last_name);
            if (isset($email)) Session::put('email', $user->email);
            if (isset($email)) Session::put('id', UserShibboleth::where('email', '=', $email)->first()->id); //TODO: Look at this


            //Group Session Field
            if (isset($email)){
                try{
                    $group = Group::whereHas('users', function($q){
                        $q->where('email', '=', Request::server(Config::get("$this->cpath.idp_login_email")));
                    })->first();

                    Session::put('group', $group->name);
                }catch(Exception $e){ // TODO: Remove later after all auth is set up.
                    Session::put('group', 'undefined');
                }
            }

            //Set session to know user is local
            Session::put('auth_type', 'local');
            return View::make('/local_landing');
        }
        else
        {
            return View::make(Config::get("$this->cpath.login_fail"));
        }
    }
    
    public function local_landing()
    {
        return View::make(Config::get("$this->cpath.default_view"));
    }

    /**
     * Setup authorization based on returned server variables
     * from the IDP.
     */
    public function idpAuthorize()
    {
        $email = Request::server(Config::get("$this->cpath.idp_login_email"));
        $first_name = Request::server(Config::get("$this->cpath.idp_login_first"));
        $last_name = Request::server(Config::get("$this->cpath.idp_login_last"));

        // Attempt to login with the email, if success, update the user model
        // with data from the Shibboleth headers (if present)
        if (Auth::attempt(array('email' => $email), true))
        {
            if (isset($first_name)) Session::put('first', $first_name);
            if (isset($last_name)) Session::put('last', $last_name);
            if (isset($email)) Session::put('email', $email);
            if (isset($email)) Session::put('id', UserShibboleth::where('email', '=', $email)->first()->id); //TODO: Check this

            //Group Session Field
            if (isset($email)){
                try
                {
                    $group = Group::with([ 'users' => function($q){
                        $q->where('email', '=', Request::server(Config::get("$this->cpath.idp_login_email")));
                    }])->first();

                    Session::put('group', $group->name);
                }
                catch(Exception $e)
                { // TODO: Remove later after all auth is set up.
                    Session::put('group', 'undefined');
                }
            }


            //Set session to know user is idp
            Session::put('auth_type', 'idp');
            
            $shib_view_config = Config::get("$this->cpath.shibboleth_view");
            //Check if route exists else redirect
            if(View::exists($shib_view_config)) return View::make($shib_view_config);
            else return Redirect::to($shib_view_config);
        }
        else
        {
            //Add user to group and send through auth.
            if(isset($email)){
                $user = UserShibboleth::create(array(
                        'email' => $email,
                        'type' => 'shibboleth',
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'enabled' => 0
                    ));
                $group = Group::find(Config::get("$this->cpath.shibboleth_group"));

                $group->users()->save($user);

                return Redirect::to('https://' . Request::server('SERVER_NAME') . ':' . Request::server('SERVER_PORT') . Config::get("$this->cpath.idp_login") . '?target=' . action($this->ctrpath . "idpAuthorize"));
            }

            return View::make(Config::get("$this->cpath.login_fail"));
        }
    }

    public function idp_landing()
    {
        return View::make(Config::get("$this->cpath.shibboleth_view"));
    }
    
    /**
     * Get current information about the session.
     */
    public function session()
    {
        echo 'Logged In: ' . ((Auth::check()) ? 'yes' : 'no') . '<br />';
        echo 'Session Information: <br />' . var_dump(Session::all());
    }
    
    /**
     * Destroy the current session and log the user out, redirect them to the main route.
     */
    public function destroy()
    {
        Auth::logout();

        if(Session::get('auth_type') == 'idp'){
            Session::flush();
            return Redirect::to('https://' . Request::server('SERVER_NAME') .Config::get("$this->cpath.port") . Config::get("$this->cpath.idp_logout"));
        }else{
            Session::flush();
            return View::make(Config::get("$this->cpath.local_logout"));
        }
    }
}
