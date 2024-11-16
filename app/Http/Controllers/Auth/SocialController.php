<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Session;

class SocialController extends Controller
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

    public function google(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return response()->json(['error' => "Don't have the code."], 400);
        }
        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_URI_REDIRECT'),
                'grant_type' => 'authorization_code',
            ]);

            $tokenData = $response->json();

            if (!isset($tokenData['access_token']) || !isset($tokenData['id_token'])) {
                throw new \Exception('Failed to fetch Google tokens.');
            }

            $accessToken = $tokenData['access_token'];

            $userResponse = Http::withHeaders(['Authorization' => "Bearer {$accessToken}"])
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            $userGoogle = $userResponse->json();

            if (!isset($userGoogle['sub'], $userGoogle['email'])) {
                throw new \Exception('Failed to fetch Google user info.');
            }

            $user = User::where('email', $userGoogle['email'])->first();

            if (!$user) {
                $avatarUrl = $userGoogle['picture'];
                $avatarPath = $this->downloadGoogleAvatar($avatarUrl, $userGoogle['sub']);

                $user = User::create([
                    'email' => $userGoogle['email'],
                    'username' => $this->generateUsername(),
                    'avatar' => $avatarPath,
                ]);
            }

            Auth::login($user, true);
            $redirectTo = route('feed');
            if (Session::has('lastProfileUrl')) {
                $redirectTo = Session::get('lastProfileUrl');
            }
            return redirect($redirectTo);
        } catch (\Exception $exception) {
            return redirect(route('home'))->with('error', $exception->getMessage());
        }
    }

    private function downloadGoogleAvatar($avatarUrl, $userId)
    {
        try {
            $response = Http::get($avatarUrl);
            if ($response->successful()) {
                $avatarPath = "avatars/{$userId}.jpg";
                Storage::put($avatarPath, $response->body());
                return $avatarPath;
            } else {
                throw new \Exception('Failed to download Google avatar.');
            }
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * Gera um username aleatório.
     */
    private function generateUsername()
    {
        return 'u' . time();
    }
}
