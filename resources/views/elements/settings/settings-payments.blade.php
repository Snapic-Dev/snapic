<div class="container mt-2">
    @if (count($payments))
        <div class="table-responsive">
            <div class="d-flex align-items-center border-bottom font-weight-bold">
                <div class="col-lg-2">
                    <select class="form-control typeSelect">
                        <option value="" disabled selected>Tipo</option>
                        <option value="deposit">Deposito</option>
                        <option value="post">Post</option>
                        <option value="tip">Gorjeta</option>
                        <option value="subscription">Inscrição</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-control statusSelect">
                        <option value="" disabled selected>Status</option>
                        <option value="pending">Pendente</option>
                        <option value="canceled">Cancelado</option>
                        <option value="approved">Aprovado</option>
                        <option value="refunded">Reembolsado</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-control dataSelect">
                        <option value="" disabled selected>Data</option>
                        <option value="asc">asc</option>
                        <option value="desc">desc</option>

                    </select>
                </div>
                @php
                    $baseUrl = url('/');
                    $urlWithdrawal = "{$baseUrl}/my/settings/wallet?active=withdraw";
                @endphp
                <div class="col-lg-7 text-right mt-2">
                    <a class="btn btn-primary btn-round withdrawalBtnDash" id="clearFilters"
                        href="{{ $urlWithdrawal }}">Saque</a>
                </div>
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
                    <div class="col-lg-4 mb-4">
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
                    </div>
                @endforeach
                <div class="d-flex align-items-center py-3 border-bottom font-weight-bold ">
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-4">
                            <div class="div4">
                                <div id="metric4" class="cardMetric no-blur-effect">
                                    <div class="headerCardMetric d-flex justify-content-between align-items-center">
                                        <p class="font-weight-bolder dashCardTitle">Status</p>
                                        <button class="metricsBtn" onclick="showMetrics(metric4)">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                    </div>
                                    <p class="dashCardMetric">
                                        {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($totalFaturamento) }}
                                    </p>
                                    <p class="text-uppercase dashCardLabel">Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="div4">
                                <div id="metric4" class="cardMetric no-blur-effect">
                                    <div class="headerCardMetric d-flex justify-content-between align-items-center">
                                        <p class="font-weight-bolder dashCardTitle">Faturamento</p>
                                        <button class="metricsBtn" onclick="showMetrics(metric4)">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                    </div>
                                    <p class="dashCardMetric">
                                        {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($totalFaturamento) }}
                                    </p>
                                    <p class="text-uppercase dashCardLabel">Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="div2">
                                <div id="metric2" class="cardMetric no-blur-effect">
                                    <div class="headerCardMetric d-flex justify-content-between align-items-center">
                                        <p class="font-weight-bolder dashCardTitle">Assinantes</p>
                                        <button class="metricsBtn" onclick="showMetrics(metric2)">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                    </div>
                                    <p class="dashCardMetric">
                                        {{ $totalAssinantes }}
                                    </p>
                                    <p class="text-uppercase dashCardLabel">Assinantes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-2 mt-5">
                    <div class="pl-5 pr-5 indicationBox">
                        <p class="font-weight-bolder dashCardTitle">Link de Indicação:</p>
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





                            <!-- Adiciona a margem esquerda aqui -->
                            <div class="d-flex indicationBox">
                                <input type="text" class="form-control text-center referralLink"
                                    value="{{ $defaultUrl }}" placeholder="{{ $urls['profile'] }}" id="copy-input">
                                <button class="btn btn-primary btn-block rounded btnCopy" type="button"
                                    id="copy-button" data-toggle="tooltip" data-placement="bottom"
                                    onclick="copyCode('.referralLink')">
                                    <ion-icon name="copy-outline"
                                        style="font-size: 1rem; vertical-align: middle;"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="pl-5 pr-5 indicationBox mt-4">
                        <p class="font-weight-bolder dashCardTitle">Link de Divulgação:</p>
                        <div class="input-group p-2 justify-content-between">
                            @php
                                $baseUrl = url('/');
                                $referralCode = Auth::user()->referral_code;
                                $username = Auth::user()->username;
                                $url = "{$baseUrl}/{$username}";
                            @endphp





                            <!-- Adiciona a margem esquerda aqui -->
                            <div class="d-flex indicationBox">
                                <input type="text" class="form-control text-center referralLink disclosureInput"
                                    value="{{ $url }}" placeholder="{{ $urls['profile'] }}"
                                    id="copy-input">
                                <button class="btn btn-primary btn-block rounded btnCopy" type="button"
                                    id="copy-button" data-toggle="tooltip" data-placement="bottom"
                                    onclick="copyCode('.disclosureInput')">
                                    <ion-icon name="copy-outline"
                                        style="font-size: 1rem; vertical-align: middle;"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col text-center py-3">
                <p>{{ __('No payments found') }}</p>
                <button class="btn btn-primary" id="clearFilters">{{ __('Clear Filters') }}</button>
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

        if (statusSelect.value !== "") {
            params.append('status', statusSelect.value);
        }

        if (dataSelect.value !== "") {
            params.append('dataFilter', dataSelect.value);
        }

        const queryString = params.toString();

        const baseUrl = `${window.location.origin}/my/settings/payments`;
        const url = `${baseUrl}?${queryString}`;
        window.location.href = url;
    }

    typeSelect.addEventListener('change', generateQuery);
    statusSelect.addEventListener('change', generateQuery);
    dataSelect.addEventListener('change', generateQuery);

    clearFiltersButton.addEventListener('click', function() {
        window.location.href = `${window.location.origin}/my/settings/payments`;
    });

    function generatePefilLink() {
        const username = `Auth::user()->username`;
        const baseUrl = `${window.location.origin}/${username}`;
        const url = `${baseUrl}?${queryString}`;
        window.location.href = url;
    }

    window.onload = function() {
        generateProfileLink();
    };
</script>
