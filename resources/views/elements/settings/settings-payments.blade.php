@if(count($payments))
    <div class="table-wrapper p-2">
        <div class="">
            <div class="col d-flex align-items-center py-3 border-bottom text-bold">
            <div class="col-lg-2 text-truncate">
                <select class="typeSelect">
                    <option value="" disabled selected>Tipo</option> <!-- Adicionei um valor vazio e selecionei para indicar que este é o texto padrão -->
                    <option value="deposit">Deposito</option>
                    <option value="post">Post</option>
                    <option value="tip">Gorjeta</option>
                    <option value="subscription">Inscrição</option>
                </select>
            </div>

            <div class="col-lg-3 text-truncate">
                <select class="statusSelect">
                    <option value="" disabled selected>Status</option> <!-- Alterado para "Status" para refletir melhor as opções -->
                    <option value="pending">Pendente</option>
                    <option value="canceled">Cancelado</option>
                    <option value="approved">Aprovado</option>
                    <option value="refunded">Reembolsado</option>
                </select>
            </div>
                <div class="col-lg-2 text-truncate">{{__('Amount')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('From')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('To')}}</div>
                <div class="col-lg-2 text-truncate">
                    <select class="dataSelect">
                        <option value="" disabled selected>Data</option> <!-- Alterado para "Status" para refletir melhor as opções -->
                        <option value="asc">asc</option>
                        <option value="desc">desc</option>
                    </select>
                </div>
            </div>
            @foreach($payments as $payment)
                <div class="col d-flex align-items-center py-3 border-bottom">
                    <div class="col-lg-2 text-truncate">
                        @if($payment->type == 'stream-access')
                            @if($payment->stream->status == 'in-progress')
                                <a href="{{route('public.stream.get',['streamID'=>$payment->stream->id,'slug'=>$payment->stream->slug])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}"> {{ucfirst(__($payment->type))}}</a>
                            @else
                                @if($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                    <a href="{{route('public.vod.get',['streamID'=>$payment->stream->id,'slug'=>$payment->stream->slug])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}"> {{ucfirst(__($payment->type))}}</a>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="{{__('Stream VOD unavailable')}}">{{ucfirst(__($payment->type))}}</span>
                                @endif
                            @endif
                        @elseif($payment->type == 'post-unlock')
                            <a  href="{{route('posts.get',['post_id'=>$payment->post->id,'username'=>$payment->receiver->username])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}">{{ucfirst(__($payment->type))}}</a>
                        @elseif($payment->type == 'tip')
                            {{ucfirst(__($payment->type))}}
                            @if($payment->post_id)
                                (<a  href="{{route('posts.get',['post_id'=>$payment->post->id,'username'=>$payment->receiver->username])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}">{{__("Post")}}</a>)
                            @elseif($payment->stream_id)
                                @if($payment->stream->status == 'in-progress')
                                    <a href="{{route('public.stream.get',['streamID'=>$payment->stream->id,'slug'=>$payment->stream->slug])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}"> ({{__("Stream")}})</a>
                                @else
                                    @if($payment->stream->settings['dvr'] && $payment->stream->vod_link)
                                        <a href="{{route('public.vod.get',['streamID'=>$payment->stream->id,'slug'=>$payment->stream->slug])}}" class="text-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'white' : 'dark') : (Cookie::get('app_theme') == 'dark' ? 'white' : 'dark'))}}"> ({{__("Stream")}})</a>
                                    @else
                                        <span data-toggle="tooltip" data-placement="top" title="{{__('Stream VOD unavailable')}}">({{__("Stream")}})</span>
                                    @endif
                                @endif
                            @else
                                ({{__("User")}})
                            @endif

                            @else
                            {{ucfirst(__($payment->type))}}
                        @endif
                    </div>

                    <div class="col-lg-3 d-flex justify-content-start align-item-center">
                        @switch($payment->status)
                            @case('approved')
                            <span class="badge badge-success">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('initiated')
                            @case('pending')
                            <span class="badge badge-info">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('canceled')
                            @case('refunded')
                            <span class="badge badge-warning">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('partially-paid')
                            <span class="badge badge-primary">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('declined')
                            <span class="badge badge-danger">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                        @endswitch
                    </div>
                    <div class="col-lg-2 text-truncate">{{$payment->decodedTaxes && Auth::user()->id == $payment->recipient_user_id ? \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount - $payment->decodedTaxes->taxesTotalAmount) : \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($payment->amount) }}</div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{route('profile',['username'=>$payment->sender->username])}}" class="text-dark-r">
                            {{$payment->sender->name}}
                        </a>
                    </div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{route('profile',['username'=>$payment->receiver->username])}}" class="text-dark-r">
                            {{$payment->receiver->name}}
                        </a>
                    </div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{route('profile',['username'=>$payment->receiver->username])}}" class="text-dark-r">
                            {{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('d/m/Y H:i') }}
                        </a>
                    </div>
                    <div class="col-lg-1 d-flex justify-content-center">
                        @if($payment->invoice_id && $payment->receiver->id !== \Illuminate\Support\Facades\Auth::user()->id && $payment->status === \App\Model\Transaction::APPROVED_STATUS)
                            <div class="dropdown {{GenericHelper::getSiteDirection() == 'rtl' ? 'dropright' : 'dropleft'}}">
                                <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle m-0 py-1 px-2" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    @include('elements.icon',['icon'=>'ellipsis-horizontal-outline','centered'=>false])
                                </a>
                                <div class="dropdown-menu">
                                    <!-- Dropdown menu links -->
                                    <a class="dropdown-item d-flex align-items-center" href="{{route('invoices.get', ['id' => $payment->invoice_id])}}">
                                        @include('elements.icon',['icon'=>'document-outline','centered'=>false,'classes'=>'mr-2']) {{__('View invoice')}}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="d-flex flex-row-reverse mt-3 mr-4">
        {{ $payments->onEachSide(1)->links() }}
    </div>
@else
<div class="table-wrapper">
        <div class="">
            <div class="col d-flex align-items-center py-3 border-bottom text-bold">
            <div class="col-lg-3 text-truncate">
                <select class="typeSelect">
                    <option value="" disabled selected>Tipo</option> <!-- Adicionei um valor vazio e selecionei para indicar que este é o texto padrão -->
                    <option value="deposit">Deposito</option>
                    <option value="post">Post</option>
                    <option value="tip">Gorjeta</option>
                    <option value="subscription">Assinaturas</option>
                </select>
            </div>

            <div class="col-lg-2 text-truncate">
                <select class="statusSelect">
                    <option value="" disabled selected>Status</option> <!-- Alterado para "Status" para refletir melhor as opções -->
                    <option value="pending">Pendente</option>
                    <option value="canceled">Cancelado</option>
                    <option value="approved">Aprovado</option>
                    <option value="refunded">Reembolsado</option>
                </select>
            </div>
                <div class="col-lg-2 text-truncate">{{__('Amount')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('From')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('To')}}</div>
                <div class="col-lg-2 text-truncate">
                    <select class="dataSelect">
                        <option value="" disabled selected>Data</option> <!-- Alterado para "Status" para refletir melhor as opções -->
                        <option value="asc">ASC</option>
                        <option value="desc">DESC</option>
                    </select>
                </div>
            </div>
</div>
@endif

<script>
    let typeSelect=document.querySelector('.typeSelect');
    let statusSelect=document.querySelector('.statusSelect');
    let dataSelect=document.querySelector('.dataSelect');

    function generateQuery() {
        const params = new URLSearchParams();

        if(typeSelect.value!=="") {
        params.append('type', typeSelect.value);
        }

        if(statusSelect.value!=="") {
        params.append('status', statusSelect.value);
        }

        if(dataSelect.value!=="") {
        params.append('dataFilter', dataSelect.value);
        }

        const queryString = params.toString();

        const baseUrl = `${window.location.origin}/my/settings/payments`;
        const url = `${baseUrl}?${queryString}`;
        window.location.href = url;
    }

    typeSelect.addEventListener('change',generateQuery)
    statusSelect.addEventListener('change',generateQuery)
    dataSelect.addEventListener('change',generateQuery)
</script>
