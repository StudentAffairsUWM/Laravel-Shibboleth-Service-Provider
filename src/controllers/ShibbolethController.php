<?php namespace Saitswebuwm\Shibboleth;

use Illuminate\Auth\GenericUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use JWTAuth;

class ShibbolethController extends Controller {

	//Config pathings
	private $cpath   = "shibboleth::shibboleth";
	private $ctrpath = "Saitswebuwm\\Shibboleth\\ShibbolethController@";

	private $sp;
	private $idp;
	private $config;

	/**
	 * Inject the user into this controller if present.
	 */
	public function __construct(GenericUser $user = null) {
		if (Config::get("$this->cpath.emulate_idp") == true) {
			$this->config         = new \Shibalike\Config();
			$this->config->idpUrl = 'idp';

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
	public function create() {
		if (Config::get("$this->cpath.emulate_idp") == true) {
			return Redirect::to(action($this->ctrpath . 'emulateLogin') . '?target=' . action($this->ctrpath . "idpAuthorize"));
		} else {
			return Redirect::to('https://' . Request::server('SERVER_NAME') . ':' . Request::server('SERVER_PORT') . Config::get("$this->cpath.idp_login") . '?target=' . action($this->ctrpath . "idpAuthorize"));
		}
	}

	/**
	 * Login for users not using the IDP
	 */
	public function localCreate() {
		return View::make(Config::get("$this->cpath.login_view"));
	}

	/**
	 * Authorize function for users not using the IdP
	 */
	// TODO: Update this with JWT stuff
	public function localAuthorize() {
		$email    = \Input::get(Config::get("$this->cpath.local_login_user_field"));
		$password = \Input::get(Config::get("$this->cpath.local_login_pass_field"));

		if (Auth::attempt(array('email' => $email, 'password' => $password), true)) {
			$user = UserShibboleth::where('email', '=', $email)->first();
			if (isset($user->first_name)) {
				Session::put('first', $user->first_name);
			}

			if (isset($user->last_name)) {
				Session::put('last', $user->last_name);
			}

			if (isset($email)) {
				Session::put('email', $user->email);
			}

			if (isset($email)) {
				Session::put('id', UserShibboleth::where('email', '=', $email)->first()->id);
			}
			//TODO: Look at this

			//Group Session Field
			if (isset($email)) {
				try {
					$group = Group::whereHas('users', function ($q) {
						$q->where('email', '=', Request::server(Config::get("$this->cpath.idp_login_email")));
					})->first();

					Session::put('group', $group->name);
				} catch (Exception $e) {
					// TODO: Remove later after all auth is set up.
					Session::put('group', 'undefined');
				}
			}

			//Set session to know user is local
			Session::put('auth_type', 'local');
			return View::make('/local_landing');
		} else {
			return View::make(Config::get("$this->cpath.login_fail"));
		}
	}

	/**
	 * Local user landing page
	 */
	public function local_landing() {
		return View::make(Config::get("$this->cpath.default_view"));
	}

	/**
	 * Setup authorization based on returned server variables
	 * from the IdP.
	 */
	public function idpAuthorize() {
		$email      = $this->getServerVariable(Config::get("$this->cpath.idp_login_email"));
		$first_name = $this->getServerVariable(Config::get("$this->cpath.idp_login_first"));
		$last_name  = $this->getServerVariable(Config::get("$this->cpath.idp_login_last"));

		// Attempt to login with the email, if success, update the user model
		// with data from the Shibboleth headers (if present)
		if (Auth::attempt(array('email' => $email), true)) {
			$user = UserShibboleth::where('email', '=', $email)->first();

			if (isset($first_name)) {
				$user->first_name = $first_name;
			}

			if (isset($last_name)) {
				$user->last_name = $last_name;
			}

			$user->save();

			$customClaims = ['auth_type' => 'idp'];
            $token        = JWTAuth::fromUser($user, $customClaims);


			//Check if route exists else redirect
			return $this->viewOrRedirect(Config::get("$this->cpath.shibboleth_view") . '?token=' . $token);

		} else {
			//Add user to group and send through auth.
			if (isset($email)) {
				if (Config::get("$this->cpath.add_new_users", true)) {
					$user = UserShibboleth::create(array(
						'email'      => $email,
						'type'       => 'shibboleth',
						'first_name' => $first_name,
						'last_name'  => $last_name,
						'enabled'    => 0,
					));
					$group = Group::find(Config::get("$this->cpath.shibboleth_group"));

					$group->users()->save($user);

					// this is simply brings us back to the session-setting branch directly above
					// TODO: refactor and split the purposes of these functions better
					return Redirect::to('https://' . Request::server('SERVER_NAME') . ':' . Request::server('SERVER_PORT') . Config::get("$this->cpath.idp_login") . '?target=' . action($this->ctrpath . "idpAuthorize"));
				} else {
					//Identify that the user was not in our database and will not be created (despite passing IdP)
					Session::put('auth_type', 'no_user');
					Session::put('group', 'undefined');

					return $this->viewOrRedirect(Config::get("$this->cpath.login_fail"));
				}
			}

			return View::make(Config::get("$this->cpath.login_fail"));
		}
	}

	public function idp_landing() {
		return View::make(Config::get("$this->cpath.shibboleth_view"));
	}

	/**
	 * Get current information about the session.
	 */
	public function session() {
		echo 'Logged In: ' . ((Auth::check()) ? 'yes' : 'no') . '<br />';
		echo 'Session Information: <br />' . var_dump(Session::all());
	}

	/**
	 * Destroy the current session and log the user out, redirect them to the main route.
	 */
	public function destroy() {
		Auth::logout();
		Session::flush();

		$token = JWTAuth::invalidate($_GET['token']);

		if (Session::get('auth_type') == 'idp') {
			if (Config::get("$this->cpath.emulate_idp") == true) {
				Session::flush();
				return Redirect::to(action($this->ctrpath . 'emulateLogout'));
			} else {
				Session::flush();
				return Redirect::to('https://' . Request::server('SERVER_NAME') . Config::get("$this->cpath.port") . Config::get("$this->cpath.idp_logout"));
			}
		} else {
			Session::flush();
			return View::make(Config::get("$this->cpath.local_logout"));
		}
	}

	function getAttrStore() {
		return new \Shibalike\Attr\Store\ArrayStore(Config::get("$this->cpath.emulate_idp_users"));
	}

	function getStateManager() {
		$session = \UserlandSession\SessionBuilder::instance()
			->setSavePath(sys_get_temp_dir())
			->setName('SHIBALIKE_BASIC')
			->build();
		return new \Shibalike\StateManager\UserlandSession($session);
	}

	public function emulateLogin() {
		$from = (Request::get('target') != null) ? Request::get('target') : $this->getServerVariable('HTTP_REFERER');

		$this->sp->makeAuthRequest($from);
		$this->sp->redirect();
	}

	public function emulateLogout() {
		$this->sp->logout();
		die('Goodbye, fair user. <a href="' . $this->getServerVariable('HTTP_REFERER') . '">Return from whence you came</a>!');
	}

	public function emulateIdp() {
		if (Request::get('username') != null) {
			$username = '';
			if (Request::get('username') === Request::get('password')) {
				$username = Request::get('username');
			}

			$userAttrs = $this->idp->fetchAttrs($username);
			if ($userAttrs) {
				$this->idp->markAsAuthenticated($username);
				$this->idp->redirect();
			} else {
				echo "Sorry. You failed to authenticate. <a href='idp'>Try again</a>";
			}
		}
		?>
			<form action="" method="post">
				<dl>
					<dt>Username</dt><dd><input size="20" name="username"></dd>
					<dt>Password</dt><dd><input size="20" name="password" type="password"></dd>
				</dl>
				<p><input type="submit" value="Login"></p>
			</form>
		<?php
}

	/**
	 * Wrapper function for getting server variables.
	 * Since Shibalike injects $_SERVER variables Laravel
	 * doesn't pick them up. So depending on if we are
	 * using the emulated IdP or a real one, we use the
	 * appropriate function.
	 */
	private function getServerVariable($variableName) {
		if (Config::get("$this->cpath.emulate_idp") == true) {
			return isset($_SERVER[$variableName]) ? $_SERVER[$variableName] : null;
		} else {
			$nonRedirect = Request::server($variableName);
			$redirect    = Request::server('REDIRECT_' . $variableName);
			return (!empty($nonRedirect)) ? $nonRedirect : $redirect;
		}
	}

	/*
	 * simple function that allows config variables to
	 * be either names of Views OR redirect routes
	 */
	// TODO: use this for all "view" variables
	private function viewOrRedirect($view) {
		if (View::exists($view)) {
			return View::make($view);
		} else {
			return Redirect::to($view);
		}
	}
}
