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
    <div class="form-group p-1">
        <label for="name" class="col-form-label required-label">{{ __('Name') }}</label>
        <div class="">
            <input id="name" type="text" placeholder="Apelido"
                class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"
                autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group p-1">
        <label for="password" class="col-form-label required-label">{{ __('Password') }}</label>
        <div class="input-group">
            <input id="password" placeholder="Senha" type="password"
                class="form-control @error('password') is-invalid @enderror" name="password"
                autocomplete="current-password">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePasswordVisibility()">
                    <ion-icon id="togglePasswordIcon" name="eye-outline"></ion-icon>
                </span>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="loginHelpers form-group d-flex flex-row-reverse">
        @if (Route::has('password.request'))
            <div class="pull-right p-1">
                @if (isset($mode) && $mode == 'ajax')
                    <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('forgot')" class=""
                        id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
                @else
                    <a href="{{ route('password.request') }}" class="text-bold"
                        id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
                @endif
            </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="form-group row mb-0 mt-4 p-1">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block">
                {{ __('Login') }}
            </button>
        </div>
    </div>
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
</script>
