<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Providers\AuthServiceProvider;
use App\Rules\IsEmailDelivrable;

class RegisterInfluencerController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
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

        // Validação do campo de idade
        return Validator::make($data, array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailValidationRule,
            'password' => ['min:6', 'required', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6'],
            'terms' => ['required'],
            'age' => ['required', 'date', function ($attribute, $value, $fail) {
                $birthDate = \Carbon\Carbon::parse($value);
                $age = \Carbon\Carbon::now()->diffInYears($birthDate);
                if ($age < 18) {
                    $fail('You must be at least 18 years old.');
                }
            }],
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


    public function showRegistrationForm()
    {
        return view('auth.register-influencer');
    }

    public function register(Request $request)
    {
        // Validação dos dados
        $this->validator($request->all())->validate();

        // Criação do usuário
        $user = $this->create($request->all());

        // Ação após o registro
        return redirect($this->redirectTo)->with('success', 'Registration successful!');
    }
}
