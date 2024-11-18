<form method="POST" action="{{ route('login') }}">
    @csrf
    @if (getSetting('social-login.facebook_client_id') ||
    getSetting('social-login.twitter_client_id') ||
    getSetting('social-login.google_client_id'))
    <div class="my-1">
        <p class="mb-0">
            {{ __("Don't have an account?") }}
            @if (isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('register')"
                class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
            @else
            <a href="{{ route('register') }}"
                class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
            @endif
        </p>
    </div>
    @endif
    <div class="form-group">
        <!-- <label for="name" class="col-form-label required-label">{{ __('Name') }}</label> -->
        <div class="">
            @php
                $inputSpace = filter_var(old('username_email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            @endphp
            <input class="inputLogin form-control @error($inputSpace) is-invalid @enderror" 
                id="username_email" type="text" placeholder="Username ou Email*"
                name="username_email" value="{{ old('username_email') }}"
                autocomplete="username_email" autofocus minlength="4">

            @error($inputSpace)
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <!-- <label for="password" class="col-form-label required-label">{{ __('Password') }}</label> -->
        <div class="d-flex align-items-center">
            <div class="w-100">
                <input class="inputLogin form-control @error('password') is-invalid @enderror" id="password" placeholder="Senha*" type="password"
                    name="password"
                    autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="tooglePass">
                <span class="" onclick="togglePasswordVisibility()">
                    <ion-icon id="togglePasswordIcon" name="eye-outline"></ion-icon>
                </span>
            </div>
        </div>
    </div>

    <div class="loginHelpers form-group d-flex justify-content-end mt-2 mb-2">
        @if (Route::has('password.request'))
        <div class="pull-right p-1">
            @if (isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('forgot')" class="text-sm"
                id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
            @else
            <a href="{{ route('password.request') }}" class="text-bold text-sm"
                id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
            @endif
        </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="form-group row mb-0 mt-4">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block btnLogin">
                {{ __('Login') }}
            </button>
        </div>
    </div>
    <button type="button" class="btnGoogle d-flex" onclick="GoToLoginGoogle()">
        <img src="{{asset('/img/logos/google-logo.svg')}}" class="social-media-icon" />
        <p class="textGoogle">Entrar com Google</p>
    </button>
</form>

@if (
!getSetting('social-login.facebook_client_id') &&
!getSetting('social-login.twitter_client_id') &&
!getSetting('social-login.google_client_id'))
<hr>
<div class="text-center py-2 p-1">
    <p class="">
        {{ __("Don't have an account?") }}
        @if (isset($mode) && $mode == 'ajax')
        <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('register')"
            class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
        @else
        <a href="{{ route('register') }}"
            class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
        @endif
    </p>
</div>
@endif

<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        var toggleIcon = document.getElementById('togglePasswordIcon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.setAttribute('name', 'eye-off-outline');
        } else {
            passwordField.type = 'password';
            toggleIcon.setAttribute('name', 'eye-outline');
        }
    }

    function getGoogleOAuthURL() {
        const options = {
            redirect_uri: "https://snapic.com.br/auth/google",
            client_id: "227086638866-eg7p7llborvuf9tqfjacp4cdolj71bdp.apps.googleusercontent.com",
            access_type: "offline",
            response_type: "code",
            prompt: "consent",
            scope: ["https://www.googleapis.com/auth/userinfo.profile", "https://www.googleapis.com/auth/userinfo.email"].join(" "),
        };

        const qs = new URLSearchParams(options);

        return `https://accounts.google.com/o/oauth2/v2/auth?${qs.toString()}`;
    }

    const GoToLoginGoogle = () => {
        window.location.href = getGoogleOAuthURL()
    }
</script>