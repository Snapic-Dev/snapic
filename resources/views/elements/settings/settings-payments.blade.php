<?php

use App\Model\ReferralCodeUsage;
use App\Model\UserList;
use App\Model\UserListMember;
use App\Model\Reward;
use App\Model\Subscription;
use Carbon\Carbon;
?>


@php
$initialDate = request()->input('initialDate');
$endDate = request()->input('endDate');
$type=request()->input('type');
@endphp


<div class="container mt-2">
    @if (count($payments))
    <div class="table-responsive">
        <div class="p-2 align-items-center border-bottom font-weight-bold pb-3">
            <div class="filterArea d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <button class="filterBtn" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="icon-white">@include('elements.icon', ['icon' => 'options-outline'])</span>
                            <div>
                    </button>
                    <div class="d-flex filterBar ml-4">
                        <p class="badgeFilterStatus justify-content-center">Pendente</p>
                        <p class="badgeFilterType justify-content-center">Gift</p>
                        <p class="badgeFilterDate justify-content-center">16/09/24 - 19/09/24</p>
                    </div>
                </div>
                @php
                $baseUrl = url('/');
                $urlWithdrawal = "{$baseUrl}/my/settings/wallet?active=withdraw";
                @endphp
                <div class="mt-2 d-flex align-items-center">
                    <a class="withdrawalBtnDash justify-content-center align-items-center"
                        href="{{ $urlWithdrawal }}">
                        Sacar
                        <ion-icon class="withdrawalIcon ml-2" name="card-outline"></ion-icon>
                    </a>
                </div>
            </div>
            <div class="collapse p-3" id="collapseExample">
                <form class="card card-body cardFilter">
                    @csrf
                    <div class="typeArea">
                        <select class="form-control typeSelect" name="type">
                            <option value="" disabled selected>Tipo</option>
                            <option value="gift">Presentes</option>
                            <option value="post">Posts</option>
                            <option value="tip">Gorjetas</option>
                            <option value="one-month-subscription">Assinatura 1 mês</option>
                            <option value="three-months-subscription">Assinatura 3 meses</option>
                            <option value="six-months-subscription">Assinatura 6 meses</option>
                            <option value="yearly-subscription">Assinatura 1 ano</option>
                            <option value="subscription-renewal">Renovações</option>
                            <option value="chat-tip">Incentivo</option>
                            <option value="stream-access">Streams</option>
                            <option value="message-unlock">Mensagens</option>
                        </select>
                    </div>
                    <!-- <div class="statusArea">
                        <select class="form-control statusSelect" name="status">
                            <option value="" disabled selected>Status</option>
                            <option value="pending">Pendente</option>
                            <option value="canceled">Cancelado</option>
                            <option value="approved">Aprovado</option>
                            <option value="refunded">Reembolsado</option>
                        </select>
                    </div> -->
                    <div class="dateArea d-flex">
                        <input class="filterInput initialDate" type="date" class="mr-2" name="initialDate" />

                        <input class="filterInput endDate" type="date" class="ml-2" name="endDate" />
                    </div>
                    <div class="d-flex btnFilterArea">
                        <button class="btnCleanFilter">
                            <ion-icon name="trash-bin-outline"></ion-icon>
                        </button>
                        <button class="btnFilter" type="submit " onclick="generateQuery()">Filtrar</button>
                    </div>
                </form>
            </div>
            <!-- @php
            $baseUrl = url('/');
            $urlWithdrawal = "{$baseUrl}/my/settings/wallet?active=withdraw";
            @endphp
            <div class="col-lg-7 text-right mt-2">
                <a class="btn btn-primary btn-round withdrawalBtnDash" id="clearFilters"
                    href="{{ $urlWithdrawal }}">Saque</a>
            </div> -->
        </div>

        <div class="row dashboardArea">
            @php
            $totalFaturamento = 0;
            $totalAssinantes = 0;
            @endphp
            @foreach ($payments as $payment)
            @php
            $totalFaturamento += $payment->amount;
            if ($payment->type == 'subscription' && $payment->status == 'approved') {
            $totalAssinantes++;
            }
            @endphp
            <!-- <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                @if ($payment->type == 'stream-access')
                                    @if ($payment->stream->status == 'in-progress')
                                        <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                            class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                            {{ ucfirst(__($payment->type)) }}
                                        </a>
                                    @else
                                        @if ($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                            <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                                class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                                {{ ucfirst(__($payment->type)) }}
                                            </a>
                                        @else
                                            <span data-toggle="tooltip" data-placement="top"
                                                title="{{ __('Stream VOD unavailable') }}">
                                                {{ ucfirst(__($payment->type)) }}
                                            </span>
                                        @endif
                                    @endif
                                @elseif($payment->type == 'post-unlock')
                                    <a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}"
                                        class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                        {{ ucfirst(__($payment->type)) }}
                                    </a>
                                @elseif($payment->type == 'tip')
                                    {{ ucfirst(__($payment->type)) }}
                                    @if ($payment->post_id)
                                        (<a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}"
                                            class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                            {{ __('Post') }}
                                        </a>)
                                    @elseif($payment->stream_id)
                                        @if ($payment->stream->status == 'in-progress')
                                            <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                                class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                                ({{ __('Stream') }})
                                            </a>
                                        @else
                                            @if ($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                                <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                                    class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                                    ({{ __('Stream') }})
                                                </a>
                                            @else
                                                <span data-toggle="tooltip" data-placement="top"
                                                    title="{{ __('Stream VOD unavailable') }}">
                                                    ({{ __('Stream') }})
                                                </span>
                                            @endif
                                        @endif
                                    @else
                                        ({{ __('User') }})
                                    @endif
                                @else
                                    {{ ucfirst(__($payment->type)) }}
                                @endif
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>{{ __('Status') }}:</strong>
                                    @switch($payment->status)
                                        @case('approved')
                                            <span class="badge bg-success">{{ ucfirst(__($payment->status)) }}</span>
                                        @break

                                        @case('initiated')
                                        @case('pending')
                                            <span class="badge bg-info">{{ ucfirst(__($payment->status)) }}</span>
                                        @break

                                        @case('canceled')
                                        @case('refunded')
                                            <span class="badge bg-warning">{{ ucfirst(__($payment->status)) }}</span>
                                        @break

                                        @case('partially-paid')
                                            <span class="badge bg-primary">{{ ucfirst(__($payment->status)) }}</span>
                                        @break

                                        @case('declined')
                                            <span class="badge bg-danger">{{ ucfirst(__($payment->status)) }}</span>
                                        @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    </div> -->
            @endforeach
            <div class="d-flex dashboardInfluencerArea align-items-center py-3 font-weight-bold">
                <div class="row dashRow mt-4 w-100">
                    <div class="dashboardInfluencer mt-4 d-flex">
                        <div class="card1 no-blur-effect">
                            <div class="d-flex headerCard">
                                <p class="font-weight-bolder dashCardTitle dashCardTitleAmount mt-3 ml-4">Total</p>
                                <ion-icon class="ml-2" name="receipt-outline"></ion-icon>
                            </div>
                            <p class="dashCardMetric">
                                {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($totalAmount) }}
                            </p>
                            <p class="subText">
                                QTD: {{ $totalCount  }}
                            </p>
                            <!-- <p class="text-uppercase dashCardLabel mt-4"><strong>{{ __('Status') }}:</strong>
                                @switch($payment->status)
                                @case('approved')
                                <span class="badge bg-success">{{ ucfirst(__($payment->status)) }}</span>
                                @break

                                @case('initiated')
                                @case('pending')
                                <span class="badge bg-info">{{ ucfirst(__($payment->status)) }}</span>
                                @break

                                @case('canceled')
                                @case('refunded')
                                <span class="badge bg-warning">{{ ucfirst(__($payment->status)) }}</span>
                                @break

                                @case('partially-paid')
                                <span class="badge bg-primary">{{ ucfirst(__($payment->status)) }}</span>
                                @break

                                @case('declined')
                                <span class="badge bg-danger">{{ ucfirst(__($payment->status)) }}</span>
                                @break
                                @endswitch
                            </p> -->
                        </div>
                        <div class="card2 no-blur-effect">
                            <div class="d-flex headerCard">
                                <p class="font-weight-bolder dashCardTitle mt-3 ml-4">Indicações</p>
                                <ion-icon class="ml-2" name="rocket-outline"></ion-icon>
                            </div>
                            @php
                            $userId = Auth::user()->id;

                            $totalIndications=0;
                            $totalIndicationsAmount=0;

                            $totalIndicationsAmount = Reward::where('to_user_id', $userId)
                            ->when($initialDate, function ($query) use ($initialDate) {
                            return $query->whereDate('created_at', '>=', $initialDate);
                            })
                            ->when($endDate, function ($query) use ($endDate) {
                            return $query->whereDate('created_at', '<=', $endDate);
                                })
                                ->sum('amount');

                                $totalIndications = ReferralCodeUsage::where('used_by', $userId)
                                ->when($initialDate, function ($query) use ($initialDate) {
                                return $query->whereDate('created_at', '>=', $initialDate);
                                })
                                ->when($endDate, function ($query) use ($endDate) {
                                return $query->whereDate('created_at', '<=', $endDate);
                                    })
                                    ->count();

                                    $totalIndicationsAmount = number_format($totalIndicationsAmount, 2, ',', '.');
                                    @endphp
                                    <p class="dashCardMetric">
                                        {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($totalIndicationsAmount) }}
                                    </p>
                                    <p class="subText">
                                        QTD: {{ $totalIndications }}
                                    </p>
                        </div>
                    </div>
                </div>
                <div class="row w-100">
                    <div class="dashboardInfluencer mt-4 d-flex">
                        <div class="card2 no-blur-effect">
                            <div class="d-flex headerCard">
                                <p class="font-weight-bolder dashCardTitle mt-3 ml-4">Assinantes</p>
                                <ion-icon class="ml-2" name="person-add-outline"></ion-icon>
                            </div>
                            @php
                            $userId = Auth::user()->id;
                            $totalSubscription = 0;

                            $totalSubscription = Subscription::where('recipient_user_id', $userId)
                            ->where('status', 'completed')
                            ->when($initialDate, function ($query, $initialDate) {
                            return $query->whereDate('created_at', '>=', $initialDate);
                            })
                            ->when($endDate, function ($query, $endDate) {
                            return $query->whereDate('created_at', '<=', $endDate);
                                })
                                ->count();
                                @endphp
                                <p class="dashCardMetric">
                                    {{ $totalSubscription }}
                                </p>
                                <p class="subText">
                                    Total
                                </p>
                        </div>
                        <div class="card2 no-blur-effect">
                            <div class="d-flex headerCard">
                                <p class="font-weight-bolder dashCardTitle mt-3 ml-4">Seguidores</p>
                                <ion-icon class="ml-2" name="people-outline"></ion-icon>
                            </div>
                            @php
                            $userId = Auth::user()->id;
                            $totalFollowers=0;

                            $listIds = UserListMember::where('user_id', $userId)
                            ->when($initialDate, function ($query, $initialDate) {
                            return $query->whereDate('created_at', '>=', $initialDate);
                            })
                            ->when($endDate, function ($query, $endDate) {
                            return $query->whereDate('created_at', '<=', $endDate);
                                })
                                ->pluck('list_id');
                                $totalFollowers=UserList::whereIn('id', $listIds)
                                ->where('type', 'following')

                                ->count();

                                @endphp
                                <p class="dashCardMetric">
                                    {{ $totalFollowers }}
                                </p>
                                <p class="subText">
                                    Total
                                </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-2 mt-5 referralArea">
                <div class="pl-5 pr-5 indicationBox">
                    <p class="dashCardTitle">Link de Indicação:</p>
                    <div class="input-group p-2 justify-content-between">
                        @php
                        $baseUrl = url('/');
                        $referralCode = Auth::user()->referral_code;
                        $username = Auth::user()->username;
                        $urls = [
                        'profile' => "{$baseUrl}/influencer/register?referral={$referralCode}",
                        'home' => "{$baseUrl}/influencer/home?referral={$referralCode}",
                        'register' => "{$baseUrl}/influencer/register?referral={$referralCode}",
                        ];

                        $defaultPage = getSetting('referrals.referrals_default_link_page');
                        $defaultUrl = $urls[$defaultPage] ?? $urls['profile'];
                        @endphp




                        <div class="d-flex indicationBox">
                            <input type="text" class="form-control text-center referralLink referralLinkIndication"
                                value="{{ $defaultUrl }}" placeholder="{{ $urls['profile'] }}" id="copy-input" readonly>
                            <button class=" btnCopy" type="button"
                                id="copy-button" data-toggle="tooltip" data-placement="bottom"
                                onclick="copyCode('.referralLink')">
                                <ion-icon name="copy-outline"
                                    style="font-size: 1rem; vertical-align: middle;"></ion-icon>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pl-5 pr-5 indicationBox mt-4">
                    <p class="dashCardTitle">Link de Divulgação:</p>
                    <div class="input-group p-2 justify-content-between">
                        @php
                        $baseUrl = url('/');
                        $referralCode = Auth::user()->referral_code;
                        $username = Auth::user()->username;
                        $url = "{$baseUrl}/{$username}";
                        @endphp





                        <div class="d-flex indicationBox">
                            <input type="text" class="form-control text-center referralLink disclosureInput"
                                value="{{ $url }}" placeholder="{{ $urls['profile'] }}"
                                id="copy-input" readonly>
                            <button class="btnCopy" type="button"
                                id="copy-button" data-toggle="tooltip" data-placement="bottom"
                                onclick="copyCode('.disclosureInput')">
                                <ion-icon name="copy-outline"
                                    style="font-size: 1rem; vertical-align: middle;"></ion-icon>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pl-5 pr-5 BoxBtnWithdrawal mt-4">
                    <a class="btnWithdrawalMobile justify-content-center align-items-center" href="http://127.0.0.1:8000/my/settings/wallet?active=withdraw">
                        <ion-icon class="withdrawalIcon ml-2 md hydrated" name="card-outline" role="img" aria-label="card outline"></ion-icon>     
                        Sacar
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="p-2 align-items-center border-bottom font-weight-bold pb-3">
            <div class="filterArea d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <button class="filterBtn" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="icon-white">@include('elements.icon', ['icon' => 'options-outline'])</span>
                            <div>
                    </button>
                    <div class="d-flex filterBar ml-4">
                        <p class="badgeFilterStatus justify-content-center">Pendente</p>
                        <p class="badgeFilterType justify-content-center">Gift</p>
                        <p class="badgeFilterDate justify-content-center">16/09/24 - 19/09/24</p>
                    </div>
                </div>
                @php
                $baseUrl = url('/');
                $urlWithdrawal = "{$baseUrl}/my/settings/wallet?active=withdraw";
                @endphp
                <div class="mt-2 d-flex align-items-center">
                    <a class="withdrawalBtnDash justify-content-center align-items-center"
                        href="{{ $urlWithdrawal }}">
                        Sacar
                        <ion-icon class="withdrawalIcon ml-2" name="card-outline"></ion-icon>
                    </a>
                </div>
            </div>
            <div class="collapse p-3" id="collapseExample">
                <form class="card card-body cardFilter">
                    @csrf
                    <div class="typeArea">
                        <select class="form-control typeSelect" name="type">
                            <option value="" disabled selected>Tipo</option>
                            <option value="gift">Presentes</option>
                            <option value="post">Posts</option>
                            <option value="tip">Gorjetas</option>
                            <option value="one-month-subscription">Assinatura 1 mês</option>
                            <option value="three-months-subscription">Assinatura 3 meses</option>
                            <option value="six-months-subscription">Assinatura 6 meses</option>
                            <option value="yearly-subscription">Assinatura 1 ano</option>
                            <option value="subscription-renewal">Renovações</option>
                            <option value="chat-tip">Incentivo</option>
                            <option value="stream-access">Streams</option>
                            <option value="message-unlock">Mensagens</option>
                        </select>
                    </div>
                    <!-- <div class="statusArea">
                        <select class="form-control statusSelect" name="status">
                            <option value="" disabled selected>Status</option>
                            <option value="pending">Pendente</option>
                            <option value="canceled">Cancelado</option>
                            <option value="approved">Aprovado</option>
                            <option value="refunded">Reembolsado</option>
                        </select>
                    </div> -->
                    <div class="dateArea d-flex">
                        <input class="filterInput" type="date" class="mr-2" name="initialDate" />

                        <input class="filterInput" type="date" class="ml-2" name="endDate" />
                    </div>
                    <div class="d-flex btnFilterArea">
                        <button class="btnCleanFilter">
                            <ion-icon name="trash-bin-outline"></ion-icon>
                        </button>
                        <button class="btnFilter" type="submit " onclick="generateQuery()">Filtrar</button>
                    </div>
                </form>
            </div>
            <!-- @php
            $baseUrl = url('/');
            $urlWithdrawal = "{$baseUrl}/my/settings/wallet?active=withdraw";
            @endphp
            <div class="col-lg-7 text-right mt-2">
                <a class="btn btn-primary btn-round withdrawalBtnDash" id="clearFilters"
                    href="{{ $urlWithdrawal }}">Saque</a>
            </div> -->
        </div>
    <div class="row">
        <div class="col text-center py-3 mt-5 nothingData">
            <p>{{ __('Nenhum dado encontrado') }}</p>
            <button class="btnCleanFilter" onclick="cleanFilter()">
                <ion-icon name="trash-bin-outline"></ion-icon>
            </button>
        </div>
    </div>
    @endif
</div>

<script>
    let referralLink = document.querySelector('.referralLink');
    let btnCopy = document.querySelector('.btnCopy');
    let typeSelect = document.querySelector('.typeSelect');
    let statusSelect = document.querySelector('.statusSelect');
    let dataSelect = document.querySelector('.dataSelect');
    let clearFiltersButton = document.getElementById('clearFilters');

    let badgeFilterType = document.querySelector('.badgeFilterType');
    let badgeFilterStatus = document.querySelector('.badgeFilterStatus');
    let badgeFilterDate= document.querySelector('.badgeFilterDate');

    let dashCardTitleAmount= document.querySelector('.dashCardTitleAmount');

    function copyCode(selector) {
        let linkRef = document.querySelector(selector).value;
        navigator.clipboard.writeText(linkRef)
            .then(() => {
                let btnCopy = document.getElementById('copy-button');
                let toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);

            })
            .catch(err => {
                console.error('Erro ao copiar: ', err);
            });
    }

    function showMetrics(divElement) {
        divElement.classList.toggle('blur-effect');
        divElement.classList.toggle('no-blur-effect');
    }


    function generateQuery() {
        const params = new URLSearchParams();

        if (typeSelect.value !== "") {
            params.append('type', typeSelect.value);
        }

        // if (statusSelect.value !== "") {
        //     params.append('status', statusSelect.value);
        // }

        // if (dataSelect.value !== "") {
        //     params.append('dataFilter', dataSelect.value);
        // }

        const queryString = params.toString();

        const baseUrl = `${window.location.origin}/my/settings/payments`;
        const url = `${baseUrl}?${queryString}`;
        window.location.href = url;
    }

    function updateBadgesFromQuery() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);


        //                     <option value="gift">Presentes</option>
        //                     <option value="post">Posts</option>
        //                     <option value="tip">Gorjetas</option>
        //                     <option value="one-month-subscription">Assinatura 1 mês</option>
        //                     <option value="three-months-subscription">Assinatura 3 meses</option>
        //                     <option value="six-months-subscription">Assinatura 6 meses</option>
        //                     <option value="yearly-subscription">Assinatura 1 ano</option>
        //                     <option value="subscription-renewal">Renovação de assinatura</option>
        //                     <option value="chat-tip">Gorjetas por chat</option>
        //                     <option value="stream-access">Acessos a live</option>
        //                     <option value="message-unlock">Desbloqueios de mensagem</option>

        let type = urlParams.get('type');

        if(dashCardTitleAmount) {

        }

        switch (type) {
            case "gift":
                type = "Presentes";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Presentes";
                }
                break;
            case "tip":
                type = "Gorjetas";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Gorjetas";
                }
                break;
            case "one-month-subscription":
                type = "A/S 1 mês";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "1 mês Assinatura";
                }
                break;
            case "three-months-subscription":
                type = "A/S 3 mês";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "3 meses Assinatura";
                }
                break;
            case "six-months-subscription":
                type = "A/S 6 mês";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "6 meses Assinatura";
                }
                break;
            case "yearly-subscription":
                type = "A/S 1 ano";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "1 ano Assinatura";
                }
                break;
            case "chat-tip":
                type = "Incentivo";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Incentivo";
                }
                break;
            case "stream-access":
                type = "Streams";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Streams";
                }
                break;
            case "subscription-renewal":
                type = "Renovações";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Renovações";
                }
                break;
            case "message-unlock":
                type = "Mensagens";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Mensagens";
                }
                break;
            case "post":
                type = "Post";
                if (dashCardTitleAmount) {
                    dashCardTitleAmount.innerText = "Post"; // Corrigido para "Post"
                }
        break;
        }       

        // let status = urlParams.get('status');
        // switch (status) {
        //     case "pending":
        //         status = "Pendente"
        //         badgeFilterStatus.style.backgroundColor = "#17C1E8"
        //         break;
        //     case "canceled":
        //         status = "Cancelado"
        //         badgeFilterStatus.style.backgroundColor = "#EA0606";
        //         break;
        //     case "approved":
        //         status = "Aprovado"
        //         badgeFilterStatus.style.backgroundColor = "#82D616";
        //         break;
        //     case "refunded":
        //         status = "Reembolsado"
        //         badgeFilterStatus.style.backgroundColor = "#ffc107";
        //         break;
        // }

        if (type !== null && type !== "") {
            badgeFilterType.innerText = type;
            badgeFilterType.style.display = "flex";
        } else {
            badgeFilterType.style.display = "none";
        }

        // if (status !== null && status !== "") {
        //     badgeFilterStatus.innerText = status;
        //     badgeFilterStatus.style.display = "flex";
        // } else {
        //     badgeFilterStatus.style.display = "none";
        // }

        let initialDateQuery = urlParams.get('initialDate');
        let endDateQuery = urlParams.get('endDate');
        
    if (initialDateQuery !== null && initialDateQuery !== "" && endDateQuery !== null && endDateQuery !== "") {
            badgeFilterDate.innerText = `${initialDateQuery} - ${endDateQuery}`
            badgeFilterDate.style.display = "flex";
        } else {
            badgeFilterDate.style.display = "none";
        }
    }

    window.onload = updateBadgesFromQuery;

    // typeSelect.addEventListener('change', generateQuery);
    // statusSelect.addEventListener('change', generateQuery);
    // dataSelect.addEventListener('change', generateQuery);

    let btnCleanFilter = document.querySelector('.btnCleanFilter');

    function cleanFilter() {
        window.location.href = `${window.location.origin}/my/settings/payments`;
        badgeFilterType.style.display = "none";
        badgeFilterStatus.style.display = "none";
        badgeFilterDate.style.display = "none";
    };

    btnCleanFilter.addEventListener('click', cleanFilter);
    // function generatePefilLink() {
    //     const username = `Auth::user()->username`;
    //     const baseUrl = `${window.location.origin}/${username}`;
    //     const url = `${baseUrl}?${queryString}`;
    //     window.location.href = url;
    // }

    // window.onload = function() {
    //     generateProfileLink();
    // };
</script>