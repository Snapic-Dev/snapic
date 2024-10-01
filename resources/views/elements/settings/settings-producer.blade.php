@if (session('success'))
    <div class="p-2 alert alert-success text-white font-weight-bold mt-2" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="p-2 alert alert-warning text-white font-weight-bold mt-2" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (Auth::user()->verification &&
        (Auth::user()->verification->rejectionReason && Auth::user()->verification->status === 'rejected'))
    <div class="p-2 alert alert-warning text-white font-weight-bold mt-2" role="alert">
        {{ __('Your previous verification attempt was rejected for the following reason:') }}
        "{{ Auth::user()->verification->rejectionReason }}"
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form class="p-2 verify-form" action="{{ route('my.settings.verify.save') }}" method="POST">
    @csrf
    <p class="p-2">
    <strong>Quer Monetizar Seu Talento?</strong> <em>Torne-se um Produtor de Conteúdo e Brilhe!</em>
    </p>
    <p class="p-2">
        Na <strong>Snapic</strong>, você terá:
    </p>
    <ul class=" d-flex align-items-start flex-column">
        <li><strong>Um Gerente de Contas</strong> dedicado para te apoiar</li>
        <li><strong>Taxas Justas</strong> que valorizam seu trabalho</li>
        <li><strong>Liberdade Financeira</strong> para viver da sua paixão</li>
    </ul>
    <p class="p-2">
        <strong>Para se tornar um Produtor de Conteúdo, siga esses 3 passos:</strong>
    </p>
   
    <div class="d-flex align-items-center mb-1 ml-4 p-2">
        @if (Auth::user()->email_verified_at)
            @include('elements.icon', [
                'icon' => 'checkmark-circle-outline',
                'variant' => 'medium',
                'classes' => 'text-success mr-2',
            ])
        @else
            @include('elements.icon', [
                'icon' => 'close-circle-outline',
                'variant' => 'medium',
                'classes' => 'text-warning mr-2',
            ])
        @endif
        <span>
            <a href="{{ url('/my/settings/profile') }}" class="text-primary">{{ __('Confirme') }} </a> 
            {{ __('seu endereço de e-mail.') }}
        </span>
    </div>
    <div class="d-flex align-items-center mb-1 ml-4 p-2">
        @if (Auth::user()->birthdate)
            @include('elements.icon', [
                'icon' => 'checkmark-circle-outline',
                'variant' => 'medium',
                'classes' => 'text-success mr-2',
            ])
        @else
            @include('elements.icon', [
                'icon' => 'close-circle-outline',
                'variant' => 'medium',
                'classes' => 'text-warning mr-2',
            ])
        @endif
        <span>
            <a href="{{ url('/my/settings/profile') }}" class="text-primary">{{ __('Defina') }} </a> 
            {{ __('sua data de nascimento') }}
        </span>
    </div>
    <div class="d-flex align-items-center ml-4 p-2">
        @if (Auth::user()->verification && Auth::user()->verification->status == 'verified')
            @include('elements.icon', [
                'icon' => 'checkmark-circle-outline',
                'variant' => 'medium',
                'classes' => 'text-success mr-2',
            ]) {{ __('Upload a Goverment issued ID card.') }}
        @else
            @if (
                !Auth::user()->verification ||
                    (Auth::user()->verification &&
                        Auth::user()->verification->status !== 'verified' &&
                        Auth::user()->verification->status !== 'pending'))
                @include('elements.icon', [
                    'icon' => 'close-circle-outline',
                    'variant' => 'medium',
                    'classes' => 'text-warning mr-2',
                ]) {{ __('Upload a Goverment issued ID card.') }}
            @else
                @include('elements.icon', [
                    'icon' => 'time-outline',
                    'variant' => 'medium',
                    'classes' => 'text-primary mr-2',
                ]) {{ __('Identity check in progress.') }}
            @endif
        @endif
    </div>
    @if (
        !Auth::user()->verification ||
            (Auth::user()->verification &&
                Auth::user()->verification->status !== 'verified' &&
                Auth::user()->verification->status !== 'pending'))
        <h5 class="mt-5 mb-4 p-2 font-weight-bold">{{ __('Complete your verification') }}</h5>
        <p class="mb-1 p-2">
            Para finalizar seu pedido de se tornar um <strong>Produtor de Conteúdo</strong>, por favor, anexe fotos nítidas de seu documento (frente e verso).
        </p>
        <p class="mb-1 p-2">
            <em>Observação:</em> Esses dados são necessários para verificar sua identidade.
        </p>
        <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
        <small class="form-text text-muted mb-2 p-2">{{ __('Allowed file types') }}:
            {{ str_replace(',', ', ', AttachmentHelper::filterExtensions('manualPayments')) }}. {{ __('Max size') }}: 4
            {{ __('MB') }}.</small>
        <div class="d-flex flex-row-reverse p-2">
            <button class="p-3 btn btn-round btn-primary btn-block mt-2">{{ __('Submit') }}</button>
        </div>
    @endif
    @if (Auth::user()->email_verified_at &&
            Auth::user()->birthdate &&
            (Auth::user()->verification && Auth::user()->verification->status == 'verified'))
        <p class="mt-3">{{ __("Your info looks good, you're all set to post new content!") }}</p>
    @endif
</form>
@include('elements.uploaded-file-preview-template')
