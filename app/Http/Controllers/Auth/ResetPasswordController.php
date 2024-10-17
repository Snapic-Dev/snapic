<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $this->redirectTo = route('home');
    }

    /**
     * Handle a password reset request for the application.
     *
     * Reset the password for the user using a token and the new password provided.
     *
     * @bodyParam token string required The password reset token sent to the user's email. Example: 123456
     * @bodyParam email string required The email address of the user. Example: johndoe@example.com
     * @bodyParam password string required The new password. Must be at least 6 characters. Example: newpassword
     * @bodyParam password_confirmation string required The confirmation of the new password. Must match the password. Example: newpassword
     *
     * @response 200 {
     *  "message": "Your password has been reset!"
     * }
     * @response 422 {
     *  "errors": {
     *     "email": [
     *       "The selected email is invalid."
     *     ],
     *     "password": [
     *       "The password must be at least 6 characters."
     *     ],
     *     "token": [
     *       "This password reset token is invalid."
     *     ]
     *  }
     * }
     */
}
