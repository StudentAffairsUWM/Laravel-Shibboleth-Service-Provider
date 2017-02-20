<?php
namespace StudentAffairsUwm\Shibboleth\Controllers;

use Illuminate\Auth\GenericUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Console\AppNamespaceDetectorTrait;
use JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShibbolethController extends Controller
{
    /**
     * Service Provider
     * @var Shibalike\SP
     */
    private $sp;

    /**
     * Identity Provider
     * @var Shibalike\IdP
     */
    private $idp;

    /**
     * Configuration
     * @var Shibalike\Config
     */
    private $config;

    /**
     * Constructor
     */
    public function __construct(GenericUser $user = null)
    {
        if (config('shibboleth.emulate_idp') == true) {
            $this->config         = new \Shibalike\Config();
            $this->config->idpUrl = '/emulated/idp';

            $stateManager = $this->getStateManager();

            $this->sp = new \Shibalike\SP($stateManager, $this->config);
            $this->sp->initLazySession();

            $this->idp = new \Shibalike\IdP($stateManager, $this->getAttrStore(), $this->config);
        }

        $this->user = $user;
    }

    /**
     * Create the session, send the user away to the IDP
     * for authentication.
     */
    public function create()
    {
        if (config('shibboleth.emulate_idp') == true) {
            return Redirect::to(action('\\' . __CLASS__ . '@emulateLogin')
                . '?target=' .  action('\\' . __CLASS__ . '@idpAuthorize'));
        }

        return Redirect::to('https://' . Request::server('SERVER_NAME')
            . ':' . Request::server('SERVER_PORT') . config('shibboleth.idp_login')
            . '?target=' . action('\\' . __CLASS__ . '@idpAuthorize'));
    }

    /**
     * Login for users not using the IdP.
     */
    public function localCreate()
    {
        return $this->viewOrRedirect(config('shibboleth.local_login'));
    }

    /**
     * Authorize function for users not using the IdP.
     */
    public function localAuthorize()
    {
        $email    = Input::get(config('shibboleth.local_login_user_field'));
        $password = Input::get(config('shibboleth.local_login_pass_field'));

        $userClass  = config('auth.providers.users.model');

        if (Auth::attempt(array('email' => $email, 'password' => $password, 'type' => 'local'), true)) {
            $user = $userClass::where('email', '=', $email)->first();

            // This is where we used to setup a session. Now we will setup a token.
            $customClaims = ['auth_type' => 'local'];
            $token        = JWTAuth::fromUser($user, $customClaims);

            // We need to pass the token... how?
            // Let's try this.
            return $this->viewOrRedirect(config('shibboleth.local_authenticated') . '?token=' . $token);
        }

        Session::flash('message', 'Invalid email or password.');
        return $this->viewOrRedirect(config('shibboleth.local_login'));
    }

    /**
     * Setup authorization based on returned server variables
     * from the IdP.
     */
    public function idpAuthorize()
    {
        $email     = $this->getServerVariable(config('shibboleth.idp_login_email'));
        $firstName = $this->getServerVariable(config('shibboleth.idp_login_first'));
        $lastName  = $this->getServerVariable(config('shibboleth.idp_login_last'));

        $userClass  = config('auth.providers.users.model');
        $groupClass = config('auth.providers.users.group_model');

        // Attempt to login with the email, if success, update the user model
        // with data from the Shibboleth headers (if present)
        // TODO: This can be simplified a lot
        if (Auth::attempt(array('email' => $email, 'type' => 'shibboleth'), true)) {
            $user = $userClass::where('email', '=', $email)->first();

            // Update the modal as necessary
            if (isset($firstName)) {
                $user->first_name = $firstName;
            }

            if (isset($lastName)) {
                $user->last_name = $lastName;
            }

            $user->save();

            // This is where we used to setup a session. Now we will setup a token.
            $customClaims = ['auth_type' => 'idp'];
            $token        = JWTAuth::fromUser($user, $customClaims);

            // We need to pass the token... how?
            // Let's try this.
            return $this->viewOrRedirect(config('shibboleth.shibboleth_authenticated') . '?token=' . $token);
        }

        //Add user to group and send through auth.
        if (isset($email) && config('shibboleth.add_new_users', true)) {
            $user = $userClass::create(array(
                'email'      => $email,
                'type'       => 'shibboleth',
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'enabled'    => 0,
            ));

            try {
                $group = $groupClass::findOrFail(config('shibboleth.shibboleth_group'));
            } catch (ModelNotFoundException $e) {
                $msg = "Could not find " . $groupClass
                    . " with primary key " . config('shibboleth.shibboleth_group')
                    . "! Check your Laravel-Shibboleth configuration.";
                throw new \RuntimeException($msg, 900, $e);
            }

            $group->users()->save($user);
        }

        // this is simply brings us back to the session-setting branch directly above
        if (config('shibboleth.emulate_idp') == true) {
            return Redirect::to(action('\\' . __CLASS__ . '@emulateLogin')
                . '?target=' . action('\\' . __CLASS__ . '@idpAuthorize'));
        }

        return Redirect::to('https://' . Request::server('SERVER_NAME')
            . ':' . Request::server('SERVER_PORT') . config('shibboleth.idp_login')
            . '?target=' . action('\\' . __CLASS__ . '@idpAuthorize'));
    }

    /**
     * Destroy the current session and log the user out, redirect them to the main route.
     */
    public function destroy()
    {
        $token = JWTAuth::parseToken();

        $payload = $token->getPayload();

        Auth::logout();
        Session::flush();
        $token->invalidate();

        if ($payload->get('auth_type') == 'idp') {
            if (config('shibboleth.emulate_idp') == true) {
                return Redirect::to(action('\\' . __CLASS__ . '@emulateLogout'));
            }

            return Redirect::to('https://' . Request::server('SERVER_NAME') . config('shibboleth.idp_logout'));
        }

        return $this->viewOrRedirect(config('shibboleth.local_logout'));
    }

    /**
     * Emulate a login via Shibalike
     */
    public function emulateLogin()
    {
        $from = (Input::get('target') != null) ? Input::get('target') : $this->getServerVariable('HTTP_REFERER');

        $this->sp->makeAuthRequest($from);
        $this->sp->redirect();
    }

    /**
     * Emulate a logout via Shibalike
     */
    public function emulateLogout()
    {
        $this->sp->logout();

        $referer = $this->getServerVariable('HTTP_REFERER');

        die("Goodbye, fair user. <a href='$referer'>Return from whence you came</a>!");
    }

    /**
     * Emulate the 'authorization' via Shibalike
     */
    public function emulateIdp()
    {
        $data = [];

        if (Input::get('username') != null) {
            $username = (Input::get('username') === Input::get('password')) ?
                Input::get('username') : '';

            $userAttrs = $this->idp->fetchAttrs($username);
            if ($userAttrs) {
                $this->idp->markAsAuthenticated($username);
                $this->idp->redirect('/idp');
            }

            $data['error'] = 'Incorrect username and/or password';
        }

        return view('IdpLogin', $data);
    }

    /**
     * Function to get an attribute store for Shibalike
     */
    private function getAttrStore()
    {
        return new \Shibalike\Attr\Store\ArrayStore(config('shibboleth.emulate_idp_users'));
    }

    /**
     * Gets a state manager for Shibalike
     */
    private function getStateManager()
    {
        $session = \UserlandSession\SessionBuilder::instance()
            ->setSavePath(sys_get_temp_dir())
            ->setName('SHIBALIKE_BASIC')
            ->build();

        return new \Shibalike\StateManager\UserlandSession($session);
    }

    /**
     * Wrapper function for getting server variables.
     * Since Shibalike injects $_SERVER variables Laravel
     * doesn't pick them up. So depending on if we are
     * using the emulated IdP or a real one, we use the
     * appropriate function.
     */
    private function getServerVariable($variableName)
    {
        if (config('shibboleth.emulate_idp') == true) {
            return isset($_SERVER[$variableName]) ?
                $_SERVER[$variableName] : null;
        }

        $variable = Request::server($variableName);

        return (!empty($variable)) ?
            $variable :
            Request::server('REDIRECT_' . $variableName);
    }

    /*
     * Simple function that allows configuration variables
     * to be either names of views, or redirect routes.
     */
    private function viewOrRedirect($view)
    {
        return (View::exists($view)) ? view($view) : Redirect::to($view);
    }
}
