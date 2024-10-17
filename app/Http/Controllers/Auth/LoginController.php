<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->redirectTo = route('feed');
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * Handle a login request to the application.
     *
     * @bodyParam username string required The username of the user. Example: johndoe
     * @bodyParam password string required The password of the user. Example: secret
     * @response 200 {
     *  "success": true,
     *  "message": "Logged in successfully."
     * }
     * @response 422 {
     *  "message": "These credentials do not match our records."
     * }
     * @response 429 {
     *  "message": "Too many login attempts. Please try again later."
     * }
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Redirect the user to the social authentication page.
     *
     * @urlParam provider string required The provider to use for authentication (e.g., facebook, google). Example: facebook
     * @response 302 {
     *  "redirect_url": "https://facebook.com/..."
     * }
     */
    public function redirectToProvider(Request $request)
    {
        return Socialite::driver($request->route('provider'))->redirect();
    }

    /**
     * Handle the social authentication callback.
     *
     * @urlParam provider string required The provider to use for authentication (e.g., facebook, google). Example: facebook
     * @response 302 {
     *  "redirect_url": "http://your-app.com/feed"
     * }
     */
    public function handleProviderCallback(Request $request)
    {
        $provider = $request->route('provider');

        try {
            $user = Socialite::driver($provider)->user();
        } catch (RequestException $e) {
            throw new \ErrorException($e->getMessage());
        }

        $userCheck = User::where('auth_provider_id', $user->id)->first();
        if ($userCheck) {
            $authUser = $userCheck;
        } else {
            try {
                $authUser = AuthServiceProvider::createUser([
                    'username' => $user->getName(),
                    'email' => $user->getEmail(),
                    'auth_provider' => $provider,
                    'auth_provider_id' => $user->id
                ]);
            } catch (\Exception $exception) {
                return redirect(route('home'))->with('error', $exception->getMessage());
            }
        }

        Auth::login($authUser, true);
        $redirectTo = route('feed');
        if (Session::has('lastProfileUrl')) {
            $redirectTo = Session::get('lastProfileUrl');
        }
        return redirect($redirectTo);
    }
}
