<?php 
use App\Model\ReferralCodeUsage;
?>

<div class="table-wrapper p-2">
    <div class="">
        <div class="col py-3 text-bold border-bottom">
            <div class="col-lg-12 text-truncate d-md-block text-center">{{ __('Your referral list') }}</div>
        </div>
        @php 
            $userIdCode=Auth::user()->referral_code;
            $usagesCode = ReferralCodeUsage::where('referral_code', $userIdCode)->get();
        @endphp
            @foreach ($usagesCode as $referral)
            <div class="col d-flex align-items-center justify-content-center py-3 border-bottom">
                <div class="pl-2">
                    @if ($referral->usedBy)
                        <img class="rounded-circle avatar avatar-Referral" src="{{ $referral->usedBy->avatar }}" alt="{{ $referral->usedBy->username }}">
                     @else
                        <img class="rounded-circle avatar avatar-Referral" src="{{ \App\Providers\GenericHelperServiceProvider::getStorageAvatarPath(null) }}" alt="Avatar">
                     @endif
                </div>
                <div class="col-lg-3 text-truncate">
                    <p class="text-dark-r mt-3">
                        {{ $referral->usedBy->username }}
                    </p>
                </div>
                <div class="col-lg-3 d-none d-md-block">
                    {{ __('Since') }}: {{ \Carbon\Carbon::parse($referral->created_at)->format('Y-m-d') }}
                </div>
                <div class="col-lg-3 text-truncate">
                    {{ __('Earned') }}:<b> {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers(\Illuminate\Support\Facades\Auth::user()->id, $referral->used_by)) }}</b>
                </div>
            </div>
            @endforeach
    </div>
</div>
