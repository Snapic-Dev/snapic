<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Operations related to user"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/impersonate/{id}",
     *     tags={"User"},
     *     summary="Impersonate a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID to impersonate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirects after impersonation"
     *     )
     * )
     */
    public function impersonate(Request $request)
    {
        $userId = $request->route('id');
        try {
            $currentUserId = Auth::user()->id;
            Auth::loginUsingId($userId);

            Session::push('previousUserId', $currentUserId);
            if (!Session::get('impersonated')) {
                Session::push('impersonated', true);
            }
        } catch (\Exception $exception) {
            return Redirect::route('voyager.users.index');
        }
        return Redirect::route('feed');
    }

    /**
     * @OA\Post(
     *     path="/leave-impersonation",
     *     tags={"User"},
     *     summary="Leave impersonation and return to admin",
     *     @OA\Response(
     *         response=302,
     *         description="Redirects after leaving impersonation"
     *     )
     * )
     */
    public function leaveImpersonation(Request $request)
    {
        $previousUserId = Session::get('previousUserId');
        try {
            Auth::loginUsingId($previousUserId);
            Session::remove('previousUserId');
            Session::remove('impersonated');
        } catch (\Exception $exception) {
            return Redirect::route('feed');
        }

        return Redirect::route('voyager.users.index');
    }
}
