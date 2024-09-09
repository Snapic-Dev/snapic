<div class="container mt-4">
    @if (count($payments))
        <div class="table-responsive">
            <div class="d-flex align-items-center py-3 border-bottom font-weight-bold">
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
                <div class="col-lg-2">
                    <select class="form-control dataSelect">
                        <option value="" disabled selected>Data</option>
                        <option value="asc">asc</option>
                        <option value="desc">desc</option>
                    </select>
                </div>
            </div>
            <div class="row mt-4">
                @foreach ($payments as $payment)
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
                                            <span class="badge bg-success">
                                                {{ ucfirst(__($payment->status)) }}
                                            </span>
                                        @break

                                        @case('initiated')
                                        @case('pending')
                                            <span class="badge bg-info">
                                                {{ ucfirst(__($payment->status)) }}
                                            </span>
                                        @break

                                        @case('canceled')
                                        @case('refunded')
                                            <span class="badge bg-warning">
                                                {{ ucfirst(__($payment->status)) }}
                                            </span>
                                        @break

                                        @case('partially-paid')
                                            <span class="badge bg-primary">
                                                {{ ucfirst(__($payment->status)) }}
                                            </span>
                                        @break

                                        @case('declined')
                                            <span class="badge bg-danger">
                                                {{ ucfirst(__($payment->status)) }}
                                            </span>
                                        @break
                                    @endswitch
                                </p>
                                <p>
                                    <strong>{{ __('Amount') }}:</strong>
                                    {{ $payment->decodedTaxes && Auth::user()->id == $payment->recipient_user_id ? \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount - $payment->decodedTaxes->taxesTotalAmount) : \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount) }}
                                </p>
                                <p>
                                    <strong>{{ __('From') }}:</strong>
                                    <a href="{{ route('profile', ['username' => $payment->sender->username]) }}"
                                        class="text-dark">
                                        {{ $payment->sender->name }}
                                    </a>
                                </p>
                                <p>
                                    <strong>{{ __('To') }}:</strong>
                                    <a href="{{ route('profile', ['username' => $payment->receiver->username]) }}"
                                        class="text-dark">
                                        {{ $payment->receiver->name }}
                                    </a>
                                </p>
                                <p>
                                    <strong>{{ __('Date') }}:</strong>
                                    {{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
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
    let typeSelect = document.querySelector('.typeSelect');
    let statusSelect = document.querySelector('.statusSelect');
    let dataSelect = document.querySelector('.dataSelect');
    let clearFiltersButton = document.getElementById('clearFilters');

    function generateQuery() {
        const params = new URLSearchParams();

        if (typeSelect.value !== "") {
            params.append('type', typeSelect.value);
        }

        if (statusSelect.value !== "") {
            params.append('status', statusSelect.value);
        }

        if (dataSelect.value !== "") {}

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
</script>
