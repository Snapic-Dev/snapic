<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Rules\IsEmailDelivrable;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $redirectRoute = route('feed');
        if (getSetting('site.redirect_page_after_register') && getSetting('site.redirect_page_after_register') == 'settings') {
            $redirectRoute = route('my.settings');
        }
        $this->redirectTo = $redirectRoute;
        $this->middleware('guest');
    }

    /**
     * Handle a registration request to the application.
     * 
     * Registers a new user and returns a success message if registration is successful.
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: johndoe@example.com
     * @bodyParam password string required The password for the user. Minimum 6 characters. Example: secret
     * @bodyParam password_confirmation string required The confirmation of the password. Must match the password.
     * @bodyParam terms boolean required Must be true, indicating acceptance of terms and conditions.
     * @bodyParam g-recaptcha-response string Optional if CAPTCHA is enabled. Example: some-captcha-token
     * 
     * @response 201 {
     *  "success": true,
     *  "message": "Register successful."
     * }
     * @response 422 {
     *  "errors": {
     *     "email": [
     *       "The email has already been taken."
     *     ],
     *     "password": [
     *       "The password must be at least 6 characters."
     *     ]
     *  }
     * }
     */
    protected function validator(array $data)
    {
        $additionalRules = [];
        if (getSetting('security.recaptcha_enabled')) {
            $additionalRules = [
                'g-recaptcha-response' => 'required|captcha'
            ];
        }

        $emailValidationRule = ['required', 'string', 'email', 'max:255', 'unique:users'];
        if (getSetting('security.enforce_email_valid_check') && getSetting('security.email_abstract_api_key')) {
            $emailValidationRule = ['required', 'string', 'email', 'max:255', 'unique:users', new IsEmailDelivrable];
        }

        return Validator::make($data, array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailValidationRule,
            'password' => ['min:6', 'required', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6'],
            'terms' => ['required'],
        ], $additionalRules));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return AuthServiceProvider::createUser($data);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Register successful.']);
        }
    }
}
