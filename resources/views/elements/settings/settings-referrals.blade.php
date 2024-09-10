<div class="table-wrapper p-2">
    <div class="">
        <div class="col py-3 text-bold border-bottom">
            <div class="col-lg-12 text-truncate d-md-block text-center">{{ __('Your referral list') }}</div>
        </div>
        @if (count($referrals ?? []) > 0)
            @foreach ($referrals as $referral)
                <!-- Conteúdo do loop
        
            @foreach ($referrals as $referral)
<div class="col d-flex align-items-center py-3 border-bottom">
            <div class="pl-2">
                @if ($referral->usedBy)
<a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}">
                    <img class="rounded-circle avatar" src="{{ $referral->usedBy->avatar }}" alt="{{ $referral->usedBy->username }}">
                </a>
@else
<a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}">
                    <img class="rounded-circle avatar" src="{{ \App\Providers\GenericHelperServiceProvider::getStorageAvatarPath(null) }}" alt="Avatar">
                </a>
@endif
            </div>
            <div class="col-lg-4 text-truncate">
                <a href="{{ route('profile', ['username' => $referral->usedBy->username]) }}" class="text-dark-r">
                    {{ $referral->usedBy->name }}
                </a>
            </div>
            <div class="col-lg-4 d-none d-md-block">
                {{ __('Since') }}: {{ \Carbon\Carbon::parse($referral->created_at)->format('Y-m-d') }}
            </div>
            <div class="col-lg-4 text-truncate">
                {{ __('Earned') }}:<b> {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers(\Illuminate\Support\Facades\Auth::user()->id, $referral->used_by)) }}</b>
            </div>
        </div>
@endforeach
        
        -->
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
