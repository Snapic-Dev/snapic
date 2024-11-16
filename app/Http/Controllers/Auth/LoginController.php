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
use Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('feed');
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('username_email');


        // Verifica se a entrada fornecida é um email válido
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';  // Se for um email, retorna 'email'
        }else {
             return 'username';

        }
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        // Determina qual campo deve ser validado (email ou username)
        $loginField = $this->username();  // Vai verificar se é um email ou nome de usuário

        // Valida o campo com base no tipo de dado
        $request->validate([
            'username_email' => 'required|string',  // Vai sempre validar o campo 'username_email'
            'password' => 'required|string',         // Valida a senha
        ]);

        // Após a validação, substituímos o campo 'username_email' pelo correto
        $request->merge([
            $loginField => $request->input('username_email'),  // Substitui 'username_email' por 'email' ou 'username'
        ]);

    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Handling 2FA stuff
        $force2FA = false;
        if (getSetting('security.enable_2fa')) {
            if (Auth::user()->enable_2fa && !in_array(AuthServiceProvider::generate2FaDeviceSignature(), AuthServiceProvider::getUserDevices(Auth::user()->id))) {
                AuthServiceProvider::generate2FACode();
                AuthServiceProvider::addNewUserDevice(Auth::user()->id);
                $force2FA = true;
            }
        }
        Session::put('force2fa', $force2FA);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Logged in successfully.']);
        }
    }

    /**
     * Redirect the user to the Facebook authentication page.
     */
    public function redirectToProvider(Request $request)
    {
        return Socialite::driver($request->route('provider'))->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleProviderCallback(Request $request)
    {
        $provider = $request->route('provider');

        try {
            $user = Socialite::driver($provider)->user();
        } catch (RequestException $e) {
            throw new \ErrorException($e->getMessage());
        }

        // Creating the user & Logging in the user
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
                // Redirect to homepage with error
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
