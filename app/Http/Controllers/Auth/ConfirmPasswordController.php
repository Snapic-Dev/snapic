<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended URL fails.
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
        $this->middleware('auth');
    }

    /**
     * Confirm the user's password before accessing protected resources.
     *
     * The user must provide their password for confirmation before proceeding with the request.
     *
     * @bodyParam password string required The current password of the authenticated user. Example: secret
     * 
     * @response 200 {
     *  "success": true,
     *  "message": "Password confirmed successfully."
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "Password confirmation failed. Please try again."
     * }
     */
}
