@if (count($payments))
    <div class="table-responsive p-3 mb-5 ">
        <div class="p-3">
            <div class="d-flex align-items-center py-3 text-bold">
                <div class="col-lg-3 text-truncate">
                    <div class="form-group">
                        <select class="custom-select mr-sm-2 p-2 px-4 statusType" id="inlineFormCustomSelect">
                            <option value="">Tipo</option>
                            <option value="stream-access">Stream</option>
                            <option value="post-unlock">Post</option>
                            <option value="tip">Gorjeta</option>
                        </select>
                    </div>
                </div>
                <div class=" col-lg-3 text-truncate">
                    <div class="form-group">
                        <select class="custom-select mr-sm-2 p-2 statusPayment px-4" id="inlineFormCustomSelect">
                            <option value="">Status</option>
                            <option value="approved">Aprovado</option>
                            <option value="canceled">Cancelado</option>
                            <option value="pending">Pendente</option>
                            <option value="refunded">Reembolsado</option>
                            <option value="partially-paid">Parcial Pago</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('Amount') }}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('From') }}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('To') }}</div>
                <div class="col-lg-3 text-truncate d-none d-md-block">
                    Data
                </div>
            </div>
            @foreach ($payments as $payment)
                <div class="col d-flex align-items-center py-3 table">
                    <div class="col-lg-3 text-truncate">
                        @if ($payment->type == 'stream-access')
                            @if ($payment->stream->status == 'in-progress')
                                <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                    class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                    {{ ucfirst(__($payment->type)) }}</a>
                            @else
                                @if ($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                    <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                        class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                        {{ ucfirst(__($payment->type)) }}</a>
                                @else
                                    <span data-toggle="tooltip" data-placement="top"
                                        title="{{ __('Stream VOD unavailable') }}">{{ ucfirst(__($payment->type)) }}</span>
                                @endif
                            @endif
                        @elseif($payment->type == 'post-unlock')
                            <a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}"
                                class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">{{ ucfirst(__($payment->type)) }}</a>
                        @elseif($payment->type == 'tip')
                            {{ ucfirst(__($payment->type)) }}
                            @if ($payment->post_id)
                                (<a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}"
                                    class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">{{ __('Post') }}</a>)
                            @elseif($payment->stream_id)
                                @if ($payment->stream->status == 'in-progress')
                                    <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                        class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                        ({{ __('Stream') }})</a>
                                @else
                                    @if ($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                        <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}"
                                            class="text-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark') }}">
                                            ({{ __('Stream') }})</a>
                                    @else
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="{{ __('Stream VOD unavailable') }}">({{ __('Stream') }})</span>
                                    @endif
                                @endif
                            @else
                                ({{ __('User') }})
                            @endif
                        @else
                            {{ ucfirst(__($payment->type)) }}
                        @endif
                    </div>

                    <div class="col-lg-3 d-flex justify-content-center">
                        @switch($payment->status)
                            @case('approved')
                                <span class="badge badge-success">
                                    {{ ucfirst(__($payment->status)) }}
                                </span>
                            @break

                            @case('initiated')
                            @case('pending')
                                <span class="badge badge-info">
                                    {{ ucfirst(__($payment->status)) }}
                                </span>
                            @break

                            @case('canceled')
                            @case('refunded')
                                <span class="badge badge-warning">
                                    {{ ucfirst(__($payment->status)) }}
                                </span>
                            @break

                            @case('partially-paid')
                                <span class="badge badge-primary">
                                    {{ ucfirst(__($payment->status)) }}
                                </span>
                            @break

                            @case('declined')
                                <span class="badge badge-danger">
                                    {{ ucfirst(__($payment->status)) }}
                                </span>
                            @break
                        @endswitch
                    </div>
                    <div class="col-lg-2 text-truncate">
                        {{ $payment->decodedTaxes && Auth::user()->id == $payment->recipient_user_id ? \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount - $payment->decodedTaxes->taxesTotalAmount) : \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount) }}
                    </div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{ route('profile', ['username' => $payment->sender->username]) }}"
                            class="text-dark-r">
                            {{ $payment->sender->name }}
                        </a>
                    </div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{ route('profile', ['username' => $payment->receiver->username]) }}"
                            class="text-dark-r">
                            {{ $payment->receiver->name }}
                        </a>
                    </div>
                    <div class="col-lg-3 text-truncate d-none d-md-block">
                        <a href="{{ route('profile', ['username' => $payment->receiver->username]) }}"
                            class="text-dark-r text-sm">
                            {{ $payment->created_at->format('d/m/Y H:i:s') }}
                        </a>
                    </div>
                    <div class="col-lg-1 d-flex justify-content-center">
                        @if (
                            $payment->invoice_id &&
                                $payment->receiver->id !== \Illuminate\Support\Facades\Auth::user()->id &&
                                $payment->status === \App\Model\Transaction::APPROVED_STATUS)
                            <div
                                class="dropdown {{ GenericHelper::getSiteDirection() == 'rtl' ? 'dropright' : 'dropleft' }}">
                                <a class="btn btn-sm text-dark-r text-hover btn-outline-{{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light') }} dropdown-toggle m-0 py-1 px-2"
                                    data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                                    aria-expanded="false">
                                    @include('elements.icon', [
                                        'icon' => 'ellipsis-horizontal-outline',
                                        'centered' => false,
                                    ])
                                </a>
                                <div class="dropdown-menu">
                                    <!-- Dropdown menu links -->
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('invoices.get', ['id' => $payment->invoice_id]) }}">
                                        @include('elements.icon', [
                                            'icon' => 'document-outline',
                                            'centered' => false,
                                            'classes' => 'mr-2',
                                        ]) {{ __('View invoice') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="d-flex justify-content-center flex-row-reverse mt-3 mr-4">
        {{ $payments->onEachSide(1)->links() }}
    </div>
@else
    <div class="table-responsive p-4 mb-5">
        <div class="d-flex align-items-center py-3 text-bold">
            <div class="col-lg-3 text-truncate">
                <div class="form-group">
                    <select class="custom-select mr-sm-2 p-2 statusType" id="inlineFormCustomSelect">
                        <option value="">Tipo</option>
                        <option value="stream-access">Stream</option>
                        <option value="post-unlock">Post</option>
                        <option value="tip">Gorjeta</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3 text-truncate">
                <div class="form-group">
                    <select class="custom-select mr-sm-2 p-2 statusPayment" id="inlineFormCustomSelect">
                        <option value="">Status</option>
                        <option value="approved">Aprovado</option>
                        <option value="canceled">Cancelado</option>
                        <option value="pending">Pendente</option>
                        <option value="refunded">Reembolsado</option>
                        <option value="partially-paid">Parcial Pago</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('Amount') }}</div>
            <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('From') }}</div>
            <div class="col-lg-2 text-truncate d-none d-md-block">{{ __('To') }}</div>
            <div class="col-lg-3 text-truncate d-none d-md-block">
                Data
            </div>
        </div>
        <div class="p-3">
            <p>{{ __('There are no payments on this account.') }}</p>
        </div>
    </div>
@endif

<script>
    let statusPayment = document.querySelector('.statusPayment');
    let statusType = document.querySelector('.statusType');

    function statusOption() {
        let selectValue = statusPayment.value;
        let selectType = statusType.value;
        console.log(selectValue);
        console.log(selectType);

        let linkFilter = "";
        console.log(linkFilter)

        if (selectValue === "") {
            linkFilter = `//localhost:8000/my/settings/payments?type=${selectType}`
        }

        if (selectType === "") {
            linkFilter = `//localhost:8000/my/settings/payments?status=${selectValue}`
        }

        linkFilter = `//localhost:8000/my/settings/payments?status=${selectValue}&type=${selectType}`;

        window.location.href = linkFilter;
        statusPayment.value = selectValue;
    }

    document.addEventListener('DOMContentLoaded', function() {
        let urlParams = new URLSearchParams(window.location.search);
        let status = urlParams.get('status');
        console.log(status);
        let type = urlParams.get('type');
        console.log(status);

        if (status) {
            statusPayment.value = status;
        }

        if (type) {
            statusType.value = type
        }
    });

    statusPayment.addEventListener('change', statusOption);
    statusType.addEventListener('change', statusOption)
</script>
