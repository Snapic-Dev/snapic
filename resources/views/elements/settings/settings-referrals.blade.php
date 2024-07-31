<div class="pb-2">
    <div class="pb-2 text-left p-4 ml-4 mr-1">
        {{ __('Copy your referral link and invite other people to get a fee from their earnings.') }}</div>
    <div class="pl-5 pr-5">
        <div class="input-group p-2">
            @php
                // Base URL da aplicação
                $baseUrl = url('/');

                // Código de referência e nome de usuário do usuário autenticado
                $referralCode = Auth::user()->referral_code;
                $username = Auth::user()->username;

                // Construção das URLs
                $profileUrl = "{$baseUrl}/influencer/register?referral={$referralCode}";
                $homeUrl = "{$baseUrl}/influencer/home?referral={$referralCode}";
                $registerUrl = "{$baseUrl}/influencer/register?referral={$referralCode}";
            @endphp
            <input type="text" class="form-control text-center referralLink"
                @switch(getSetting('referrals.referrals_default_link_page')) @case('profile') value="{{ $profileUrl }}" @break @case('home') value="{{ $homeUrl }}" @break @case('register') value="{{ $registerUrl }}" @break @endswitch
                placeholder="{{ $profileUrl }}" id="copy-input">
            <div class="input-group-append">
                <button class="btn btn-primary btn-block rounded mr-0 text-truncate btnCopy" type="button"
                    id="copy-button" data-toggle="tooltip" data-placement="bottom" onclick="copyCodePix()">
                    {{ __('Copiar') }}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="table-wrapper p-2">
    <div class="">
        <div class="col py-3 text-bold border-bottom">
            <div class="col-lg-12 text-truncate d-md-block text-center">{{ __('Your referral list') }}</div>
        </div>
        @if (count($referrals))
            @foreach ($referrals as $referral)
                <div class="col d-flex align-items-center py-3 border-bottom">
                    <div class="pl-2">
                        @if ($referral->usedBy)
                            <a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}">
                                <img class="rounded-circle avatar" src="{{ $referral->usedBy->avatar }}"
                                    alt="{{ $referral->usedBy->username }}">
                            </a>
                        @else
                            <a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}">
                                <img class="rounded-circle avatar"
                                    src="{{ \App\Providers\GenericHelperServiceProvider::getStorageAvatarPath(null) }}"
                                    alt="Avatar">
                            </a>
                        @endif
                    </div>
                    <div class="col-lg-4 text-truncate">
                        <a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}"
                            class="text-dark-r">
                            {{ $referral->usedBy->name }}
                        </a>
                    </div>
                    <div class="col-lg-4 d-none d-md-block">
                        {{ __('Since') }}: {{ \Carbon\Carbon::parse($referral->created_at)->format('Y-m-d') }}
                    </div>
                    <div class="col-lg-4 text-truncate">
                        {{ __('Earned') }}:<b>
                            {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers(\Illuminate\Support\Facades\Auth::user()->id, $referral->used_by)) }}</b>
                    </div>
                </div>
            @endforeach
            <div class="d-flex flex-row-reverse mt-3 mr-4">
                {{ $referrals->onEachSide(1)->links() }}
            </div>
        @else
            <div class="p-3 text-center">
                <p>{{ __('There are no referrals to show.') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
    let referralLink = document.querySelector('.referralLink')
    let btnCopy = document.querySelector('.btnCopy')

    function copyCodePix() {
        let linkRef = referralLink.value
        navigator.clipboard.writeText(linkRef)
            .then(() => {
                sessionStorage.setItem('valorCopiado', linkRef);
                btnCopy.innerText = 'Copiado';
                setTimeout(() => {
                    btnCopy.innerText = 'Copiar';
                    btnCopy.setAttribute('title', 'Copiar');
                }, 1500);
            })
            .catch(err => {
                console.error('Erro ao copiar: ', err);
            });
    }
</script>
