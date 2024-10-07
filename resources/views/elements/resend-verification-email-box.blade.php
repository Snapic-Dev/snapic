<div class="p-4 alert alert-primary alert-dismissable text-white unverified-email-box" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="text-bold p-4 confirmationEmailTxt">Confirme seu email</h5>
    <p class="m-0 p-3 text-md ml-2">{{ __('You have not confirmed your email address yet') }}. {{ __('Click on') }} <a
            class="text-bold text-white resend-verification-btn" href="javascript:void(0)"
            onClick="sendEmailConfirmation()">{{ __('this link') }}</a> {{ __('to resend the confirmation email') }}.
    </p>
</div>
