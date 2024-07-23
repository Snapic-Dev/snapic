@php
$nichos = [
'Tecnologia',
'Saúde',
'Finanças',
'Educação',
'Moda',
'Esportes',
'Entretenimento',
'Viagens',
'Alimentação',
'Beleza',
];
@endphp

<form method="POST" action="{{ route('register-influencer') }}" id="register-influencer-form">
    @csrf

    @if (getSetting('social-login.facebook_client_id') ||
    getSetting('social-login.twitter_client_id') ||
    getSetting('social-login.google_client_id'))
    <div class="my-1">
        <p class="mb-0">
            {{ __('Already got an account?') }}
            @if (isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('login')" class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
            @else
            <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
            @endif
        </p>
    </div>
    @endif

    <div class="form-group">
        <label for="name" class="col-form-label required-label">{{ __('Apelido') }}</label>
        <div class="">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" autofocus>
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="email" class="col-form-label required-label">{{ __('E-Mail Address') }}</label>
        <div class="">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="cpf" class="col-form-label required-label">{{ __('CPF') }}</label>
        <div class="">
            <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf" value="{{ old('cpf') }}" required autocomplete="cpf">
            @error('cpf')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="age" class="col-form-label required-label">{{ __('Data Nascimento') }}</label>
        <div class="">
            <input id="age" type="date" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ old('age') }}" required autocomplete="date">
            @error('age')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="frontDoc" class="col-form-label required-label">{{ __('RG ou CNH- Frente') }}</label>
        <input id="frontDoc" type="file" class="form-control @error('frontDoc') is-invalid @enderror" name="frontDoc" value="{{ old('frontDoc') }}" required autocomplete="file" accept=".jpg, .jpeg, .png, .webp" onchange="previewImage(this, document.getElementById('frontPreview'))">
        <div class="preview">
            <img id="frontPreview" src="#" alt="Preview da CNH - Frente" style="display: none; max-height: 200px;">
        </div>
        @error('frontDoc')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="backDoc" class="col-form-label required-label">{{ __('RG ou CNH- Verso') }}</label>
        <input id="backDoc" type="file" class="form-control @error('backDoc') is-invalid @enderror" name="backDoc" accept=".jpg, .jpeg, .png, .webp" required onchange="previewImage(this, document.getElementById('backPreview'))">
        <div class="preview">
            <img id="backPreview" src="#" alt="Preview da CNH - Verso" style="display: none; max-height: 200px;">
        </div>
        @error('backDoc')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="telefone" class="col-form-label required-label">{{ __('telephone') }}</label>
        <div class="">
            <input id="telefone" type="tel" class="form-control @error('telefone') is-invalid @enderror" name="telefone" value="{{ old('telefone') }}" required autocomplete="text">
            @error('telefone')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="nicho" class="col-form-label required-label">{{ __('Nicho') }}</label>
        <div class="">
            <select id="nicho" class="form-control @error('nicho') is-invalid @enderror" name="nicho" required>
                <option value="">{{ __('Selecione um nicho') }}</option>
                @foreach ($nichos as $nicho)
                <option value="{{ $nicho }}" {{ old('nicho') == $nicho ? 'selected' : '' }}>
                    {{ $nicho }}
                </option>
                @endforeach
            </select>
            @error('nicho')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="password" class="col-form-label required-label">{{ __('Password') }}</label>
        <div class="">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="password-confirm" class="col-form-label required-label">{{ __('Confirm Password') }}</label>
        <div class="">
            <input id="password-confirm" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <div class="">
                <input class="custom-control-input @error('terms') is-invalid @enderror" id="tosAgree" type="checkbox" name="terms" value="1" placeholder="{{ __('Terms and Conditions') }}">
                <label class="custom-control-label" for="tosAgree">
                    <span>{{ __('I agree to the') }} <a href="{{ route('pages.get', ['slug' => GenericHelper::getTOSPage()->slug]) }}">{{ __('Terms of Use') }}</a>
                        {{ __('and') }} <a href="{{ route('pages.get', ['slug' => GenericHelper::getPrivacyPage()->slug]) }}">{{ __('Privacy Policy') }}</a>.</span>
                </label>
                @error('terms')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
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

    <div class="form-group row mb-0">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block">
                {{ __('Register') }}
            </button>
        </div>
    </div>

</form>
@if (
!getSetting('social-login.facebook_client_id') &&
!getSetting('social-login.twitter_client_id') &&
!getSetting('social-login.google_client_id'))
<hr>
<div class=" text-center">
    <p class="mb-4">
        {{ __('Already got an account?') }}
        @if (isset($mode) && $mode == 'ajax')
        <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('login')" class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
        @else
        <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">{{ __('Sign in') }}</a>
        @endif
    </p>
</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('register-influencer-form');
        const url = new URL(window.location.href);
        const queryParams = url.search;
        form.action = form.action + queryParams;
    });
</script>