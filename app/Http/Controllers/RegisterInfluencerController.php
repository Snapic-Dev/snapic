<?php

namespace App\Http\Controllers;

use App\Model\ReferralCodeUsage;
use App\Model\UserVerify;
use App\Models\Niche;
use App\Providers\AuthServiceProvider;
use App\Providers\FirebaseProvider;
use App\Rules\IsEmailDelivrable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterInfluencerController extends Controller
{
    protected $redirectTo;
    protected $providerFirebase;

    /**
     * PaymentsController constructor.
     * @param FirebaseProvider $providerFirebase
     */

    public function __construct(FirebaseProvider $providerFirebase)
    {
        $this->providerFirebase = $providerFirebase;

        $redirectRoute = route('feed');
        if (getSetting('site.redirect_page_after_register') && getSetting('site.redirect_page_after_register') == 'settings') {
            $redirectRoute = route('my.settings');
        }
        $this->redirectTo = $redirectRoute;
        $this->middleware('guest');
    }

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

        $nameValidationRule = ['required', 'string', 'max:255', 'unique:users'];
        if (getSetting('security.enforce_name_valid_check')) {
        }

        $cpfValidationRule = ['required', 'string', 'regex:/^\d{11}$/', 'unique:users'];
        if (getSetting('security.enforce_cpf_valid_check')) {
        }


        return Validator::make($data, array_merge([
            'name' => $nameValidationRule,
            'email' => $emailValidationRule,
            'password' => ['min:6', 'required', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6'],
            'terms' => ['required'],
            'birthdate' => ['required', 'date', function ($attribute, $value, $fail) {
                $birthDate = \Carbon\Carbon::parse($value);
                $birthdate = \Carbon\Carbon::now()->diffInYears($birthDate);
                if ($birthdate < 18) {
                    $fail('You must be at least 18 years old.');
                }
            }],
            'cpf' => $cpfValidationRule,
            'instagram' => ['nullable', 'string', 'regex:/^(https?:\/\/)?(www\.)?instagram\.com\/[a-zA-Z0-9_\.]+\/?$/', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^\+?\d{10,15}$/'],
        ], $additionalRules));
    }

    protected function create(array $data)
    {
        return AuthServiceProvider::createInfluencer($data);
    }

    protected function registered(Request $request, $user)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Register successful.']);
        }
    }

    public function showRegistrationForm()
    {
        $niches = Niche::all();
        return view('auth.register-influencer', compact('niches'));
    }

    public function register(Request $request)
    {
        ini_set('memory_limit', '256M');

        if ($request->hasFile('frontDoc') && $request->hasFile('backDoc')) {
            $frontDocUrl = $this->providerFirebase->uploadToFirebase($request->file('frontDoc'));
            $backDocUrl = $this->providerFirebase->uploadToFirebase($request->file('backDoc'));
        } else {
            return redirect()->back()->withErrors(['msg' => 'Both documents are required.']);
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        $referral = $request->query('referral');

        if ($referral) {
            ReferralCodeUsage::create([
                'used_by' => $user->id,
                'referral_code' => $referral,
            ]);
        }
        UserVerify::create([
            'user_id' => $user->id,
            'doc_front' => $frontDocUrl,
            'doc_back' => $backDocUrl,
        ]);

        return redirect($this->redirectTo)->with('success', 'Registration successful!');
    }
}
