@extends('layouts.no-nav')
@section('page_title', __('Register'))


@section('page_description', getSetting('site.description'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' . __('Register'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())


@if (getSetting('security.recaptcha_enabled') && !Auth::check())
    @section('meta')
        {!! NoCaptcha::renderJs() !!}
    @stop
@endif

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
                                                srcset="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/snapic-vert-white.png' : '/img/snapic-vert-black.png') : (Cookie::get('app_theme') == 'dark' ? '/img/snapic-vert-white.png' : '/img/snapic-vert-black.png')) }}"
                                                class="brand-logo-form"
                                                >
                                            <img
                                            src="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/snapic-white.svg' : '/img/snapic-black.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/snapic-white.png' : '/img/snapic-black.png')) }}"
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
                                @include('auth.register-influencer-form')
                                @include('auth.social-login-box')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 w-100 h-100 BannerLoginInfluencer" style="background-image: url('{{ asset('/img/BannerLogin.svg') }}'); background-size: cover; background-position: center;"></div>
            </div>
        </div>
    </div>
@endsection
