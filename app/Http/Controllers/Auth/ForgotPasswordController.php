<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Request a password reset link.
     *
     * This endpoint sends a password reset link to the user's email.
     *
     * @bodyParam email string required The email address of the user requesting the password reset. Example: johndoe@example.com
     * 
     * @response 200 {
     *  "success": true,
     *  "message": "We have emailed your password reset link!"
     * }
     * @response 419 {
     *  "success": false,
     *  "errors": {
     *      "email": "We can't find a user with that email address."
     *  }
     * }
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => trans($response), 'errors' => []]);
        }

        return back()->with('status', trans($response));
    }

    /**
     * Handle a failed password reset link request.
     *
     * @response 419 {
     *  "success": false,
     *  "errors": {
     *      "email": "We can't find a user with that email address."
     *  }
     * }
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'errors' => ['email' => trans($response)]], 419);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
