@extends('layouts.no-nav')
@section('meta')
<meta name="robots" content="noindex">
@stop

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">
        <div class="col-md-6">
            <div class="login d-flex align-items-center py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-xl-6 mx-auto">
                            <a href="{{ action('HomeController@index') }}">
                                <picture class="logoArea">
                                                <source media="(max-width: 600px)" 
                                                    srcset="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/snapic-white.svg' : '/img/snapic-black.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/snapic-vert-white.png' : '/img/snapic-vert-black.png')) }}"
                                                    class="brand-logo-form"
                                                    >
                                                <img
                                                src="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/snapic-white.svg' : '/img/snapic-black.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/snapic-white.png' : '/img/snapic-black.png')) }}"
                                                alt="Descrição"
                                                class="brand-logo-form"
                                                > 
                                </picture>  
                            </a>
                            @if (session('status'))
                            <div class="alert alert-success text-white" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif
                            @include('auth.passwords.email-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 w-100 h-100 BannerLogin"></div>
        </div>
    </div>
</div>
@endsection