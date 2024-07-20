<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfluencerRegisterController extends Controller
{
    /**
     * Show the influencer registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register-influencer');
    }
}