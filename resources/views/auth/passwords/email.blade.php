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
                            <a href="{{ action('HomeController@index') }}" class="d-flex align-items-center linkAreaLoginLogo">
                                        <picture class="logoArea">
                                                <source media="(max-width: 600px)" 
                                                srcset="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/LogoWhiteVert.svg' : '/img/LogoBlackVert.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/LogoWhiteVert.svg' : '/img/LogoBlackVert.svg')) }}"
                                                    >
                                                <img
                                                src="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/LogoWhiteHr.svg' : '/img/LogoBlackHr.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/LogoWhiteHr.svg' : '/img/LogoBlackHr.svg')) }}"
                                                alt="Descrição"
                                                class="brand-logo-form"
                                                > 
                                        </picture>
                            </a>
                            <div class="mb-5">
                                    <p class="subtitleLogin">
                                        Entre agora e se <span class="markText">divirta</span> com os seus <span class="markText">criadores favoritos</span>.
                                    </p>
                                </div>
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
            <div class="d-flex m-0 p-0 w-100 h-100 BannerLogin" style="background-image: url('{{ asset('/img/BannerLogin.svg') }}'); background-size: cover; background-position: center;"></div>
        </div>
    </div>
</div>
@endsection