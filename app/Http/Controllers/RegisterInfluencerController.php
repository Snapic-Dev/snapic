<?php

namespace App\Http\Controllers;

use App\Model\ReferralCodeUsage;
use App\Model\UserVerify;
use App\Model\Niche;
use App\Providers\AuthServiceProvider;
use App\Providers\FirebaseProvider;
use App\Rules\IsEmailDelivrable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Influencer",
 *     description="Operations related to influencer registration"
 * )
 */
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
            'website' => ['nullable', 'string', 'regex:/^(https?:\/\/)?(www\.)?instagram\.com\/[a-zA-Z0-9_\.]+\/?$/', 'unique:users'],
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

    /**
     * Show the influencer registration form.
     *
     * @OA\Get(
     *     path="/influencer/register",
     *     tags={"Influencer"},
     *     summary="Show the influencer registration form",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved registration form",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="form", type="string", description="HTML form for registration")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $niches = Niche::all();
        return view('auth.register-influencer', compact('niches'));
    }

    /**
     * Handle the influencer registration request.
     *
     * @OA\Post(
     *     path="/influencer/register",
     *     tags={"Influencer"},
     *     summary="Handle the influencer registration request",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_confirmation", "terms", "age"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="password_confirmation", type="string", example="password123"),
     *                 @OA\Property(property="terms", type="boolean", example=true),
     *                 @OA\Property(property="age", type="string", format="date", example="2005-07-16")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="redirect_to", type="string", example="/feed"),
     *             @OA\Property(property="message", type="string", example="Registration successful!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed.")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
