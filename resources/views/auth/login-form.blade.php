<form method="POST" action="{{ route('login') }}">
    @csrf
    @if (getSetting('social-login.facebook_client_id') ||
    getSetting('social-login.twitter_client_id') ||
    getSetting('social-login.google_client_id'))
    <div class="my-1">
        <p class="mb-0">
            {{ __("Don't have an account?") }}
            @if (isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('register')" class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
            @else
            <a href="{{ route('register') }}" class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
            @endif
        </p>
    </div>
    @endif
    <div class="form-group">
        <label for="name" class="col-form-label">{{ __('Name') }}</label>
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
        <label for="password" class="col-form-label">{{ __('Password') }}</label>
        <div class="">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="loginHelpers form-group d-flex flex-row-reverse">
        @if (Route::has('password.request'))
        <div class="pull-right">
            @if (isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('forgot')" class="" id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
            @else
            <a href="{{ route('password.request') }}" class="" id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
            @endif
        </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="form-group row mb-0 mt-4">
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
<div class="text-center py-2">
    <p class="">
        {{ __("Don't have an account?") }}
        @if (isset($mode) && $mode == 'ajax')
        <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('register')" class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
        @else
        <!-- <a href="{{ route('register') }}"
                    class="text-primary text-gradient font-weight-bold">{{ __('Sign up') }}</a>
                 -->
        <a id="registerLink" class="text-primary text-gradient font-weight-bold" style="cursor: pointer;">{{ __('Sign up') }}</a>

        <!-- <a class="nav-link" href="#" id="registerLink">{{ __('Register') }}</a> -->

        @endif
    </p>
</div>
@endif


<!-- Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Opção de Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="assinanteCheckbox">
                    <label class="form-check-label" for="assinanteCheckbox">
                        Sou Assinante
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="influencerCheckbox">
                    <label class="form-check-label" for="influencerCheckbox">
                        Sou Influencer
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="confirmRegisterBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>