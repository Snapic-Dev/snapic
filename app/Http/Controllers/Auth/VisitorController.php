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

class VisitorController extends Controller
{

    public function register(Request $request)
    {
        $user = AuthServiceProvider::createVisitant($request->get('id'));
        Auth::login($user, true);
    }
}
