<form method="POST" action="{{ route('register') }}" id="register-form">
    @csrf


    @if (getSetting('social-login.facebook_client_id') ||
            getSetting('social-login.twitter_client_id') ||
            getSetting('social-login.google_client_id'))
        <div class="my-1">
            <p class="mb-0">
                {{ __('Already got an account?') }}
                @if (isset($mode) && $mode == 'ajax')
                    <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('login')"
                        class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
                @endif
            </p>
        </div>
    @endif

    <div class="form-group">
        <!-- <label for="name" class="col-form-label required-label">{{ __('Apelido') }}</label> -->
        <div class="">
            <input id="name" type="text" placeholder="Apelido*"
                class="inputLogin form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"
                autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <!-- <label for="email" class=" col-form-label required-label">{{ __('E-Mail Address') }}</label> -->
        <div class="">
            <input id="email" type="email" placeholder="Email*"
                class="inputLogin form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
                autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <!-- <label for="password" class=" col-form-label required-label">{{ __('Password') }}</label> -->
        <div class="">
            <input id="password" type="password" placeholder="Senha*"
                class="inputLogin form-control @error('password') is-invalid @enderror" name="password"
                autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <!-- <label for="password-confirm" class=" col-form-label required-label">{{ __('Confirm Password') }}</label> -->

        <div class="">
            <input id="password-confirm" type="password" placeholder="Confirmar senha*"
                class="inputLogin form-control @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation" autocomplete="new-password">
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group p-2">
        <div class="custom-control custom-checkbox mt-3 mb-3">
            <div class="">
                <input class="custom-control-input @error('terms') is-invalid @enderror" id="tosAgree" type="checkbox"
                    name="terms" value="1" placeholder="{{ __('Terms and Conditions') }}">
                <label class="custom-control-label" for="tosAgree">
                    <span>{{ __('I agree to the') }} <a
                            href="{{ route('pages.get', ['slug' => GenericHelper::getTOSPage()->slug]) }}">{{ __('Terms of Use') }}</a>
                        {{ __('and') }} <a
                            href="{{ route('pages.get', ['slug' => GenericHelper::getPrivacyPage()->slug]) }}">{{ __('Privacy Policy') }}</a>.</span>
                </label>
            </div>
        </div>
    </div>

    @if (getSetting('security.recaptcha_enabled') && !Auth::check())
        <div class="form-group row d-flex justify-content-center captcha-field">
            {!! NoCaptcha::display([
                'data-theme' => Cookie::get('app_theme') == null ? getSetting('site.default_user_theme') : Cookie::get('app_theme'),
            ]) !!}
            @error('g-recaptcha-response')
                <span class="text-danger" role="alert">
                    <strong>{{ __('Please check the captcha field.') }}</strong>
                </span>
            @enderror
        </div>
    @endif

    <div class="form-group row mb-0 p-1">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block btnLogin">
                {{ __('Register') }}
            </button>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col">


            <a href="{{ route('register-influencer-form') }}" class="border btn btn-grow btn-lg btn-color btn-block btnToSwitchRegister">
                {{ __('Quero ser Influencer') }}
            </a>

        </div>
    </div>

</form>
@if (
    !getSetting('social-login.facebook_client_id') &&
        !getSetting('social-login.twitter_client_id') &&
        !getSetting('social-login.google_client_id'))
    <hr>
    <div class="text-center p-1">
        <p class="mb-4">
            {{ __('Already got an account?') }}
            @if (isset($mode) && $mode == 'ajax')
                <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('login')"
                    class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
            @else
                <a href="{{ route('login') }}"
                    class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
            @endif
        </p>
    </div>
@endif
