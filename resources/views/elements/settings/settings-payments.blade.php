@if(count($payments))
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('From') }}</th>
                <th>{{ __('To') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>
                    @if($payment->type == 'stream-access')
                        @if($payment->stream->status == 'in-progress')
                            <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                                {{ ucfirst(__($payment->type)) }}
                            </a>
                        @else
                            @if($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                                    {{ ucfirst(__($payment->type)) }}
                                </a>
                            @else
                                <span data-toggle="tooltip" data-placement="top" title="{{ __('Stream VOD unavailable') }}">
                                    {{ ucfirst(__($payment->type)) }}
                                </span>
                            @endif
                        @endif
                    @elseif($payment->type == 'post-unlock')
                        <a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                            {{ ucfirst(__($payment->type)) }}
                        </a>
                    @elseif($payment->type == 'tip')
                        {{ ucfirst(__($payment->type)) }}
                        @if($payment->post_id)
                            (<a href="{{ route('posts.get', ['post_id' => $payment->post->id, 'username' => $payment->receiver->username]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                                {{ __("Post") }}
                            </a>)
                        @elseif($payment->stream_id)
                            @if($payment->stream->status == 'in-progress')
                                <a href="{{ route('public.stream.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                                    ({{ __("Stream") }})
                                </a>
                            @else
                                @if($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                    <a href="{{ route('public.vod.get', ['streamID' => $payment->stream->id, 'slug' => $payment->stream->slug]) }}" class="text-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark')) }}">
                                        ({{ __("Stream") }})
                                    </a>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="{{ __('Stream VOD unavailable') }}">
                                        ({{ __("Stream") }})
                                    </span>
                                @endif
                            @endif
                        @else
                            ({{ __("User") }})
                        @endif
                    @else
                        {{ ucfirst(__($payment->type)) }}
                    @endif
                </td>
                <td>
                    @switch($payment->status)
                        @case('approved')
                            <span class="badge badge-success">{{ ucfirst(__($payment->status)) }}</span>
                            @break
                        @case('initiated')
                        @case('pending')
                            <span class="badge badge-info">{{ ucfirst(__($payment->status)) }}</span>
                            @break
                        @case('canceled')
                        @case('refunded')
                            <span class="badge badge-warning">{{ ucfirst(__($payment->status)) }}</span>
                            @break
                        @case('partially-paid')
                            <span class="badge badge-primary">{{ ucfirst(__($payment->status)) }}</span>
                            @break
                        @case('declined')
                            <span class="badge badge-danger">{{ ucfirst(__($payment->status)) }}</span>
                            @break
                    @endswitch
                </td>
                <td>
                    {{ $payment->decodedTaxes && Auth::user()->id == $payment->recipient_user_id ? \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount - $payment->decodedTaxes->taxesTotalAmount) : \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount) }}
                </td>
                <td>
                    <a href="{{ route('profile', ['username' => $payment->sender->username]) }}" class="text-dark-r">
                        {{ $payment->sender->name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('profile', ['username' => $payment->receiver->username]) }}" class="text-dark-r">
                        {{ $payment->receiver->name }}
                    </a>
                </td>
                <td>
                    @if($payment->invoice_id && $payment->receiver->id !== \Illuminate\Support\Facades\Auth::user()->id && $payment->status === \App\Model\Transaction::APPROVED_STATUS)
                    <div class="dropdown {{ GenericHelper::getSiteDirection() == 'rtl' ? 'dropright' : 'dropleft' }}">
                        <a class="btn btn-sm text-dark-r text-hover btn-outline-{{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light')) }} dropdown-toggle m-0 py-1 px-2" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            @include('elements.icon', ['icon' => 'ellipsis-horizontal-outline', 'centered' => false])
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('invoices.get', ['id' => $payment->invoice_id]) }}">
                                @include('elements.icon', ['icon' => 'document-outline', 'centered' => false, 'classes' => 'mr-2']) {{ __('View invoice') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex flex-row-reverse mt-3 mr-4">
        {{ $payments->onEachSide(1)->links() }}
    </div>
</div>
@else
<div class="p-3">
    <p>{{ __('There are no payments on this account.') }}</p>
</div>
@endif
