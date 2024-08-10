@extends('layouts.user-no-nav')
@section('page_title', $stream->name)


@section('styles')
<link rel="stylesheet" href="{{asset('/libs/video.js/dist/video-js.min.css')}}">
<link rel="stylesheet" href="{{asset('/css/player-theme.css')}}">
{!!
Minify::stylesheet([
'/libs/dropzone/dist/dropzone.css',
'/css/pages/checkout.css',
'/css/pages/stream.css',
])->withFullUrl()
!!}
@stop

@section('scripts')
<script type="text/javascript" src="{{asset('/libs/video.js/dist/video.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/libs/videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/libs/videojs-http-source-selector/dist/videojs-http-source-selector.min.js')}}"></script>
{!!
Minify::javascript([
'/libs/dropzone/dist/dropzone.js',
'/js/FileUpload.js',
'/js/pages/stream.js',
'/js/pages/lists.js',
'/libs/videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min.js',
'/libs/videojs-http-source-selector/dist/videojs-http-source-selector.min.js',
'/libs/pusher-js-auth/lib/pusher-auth.js',
'/js/pages/checkout.js',
])->withFullUrl()
!!}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="pt-4 d-flex justify-content-between align-items-center px-3 pb-3 border-bottom">
            <h5 class="text-truncate text-bold mb-0 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{$stream->name}}</h5>
            @if(!isset($streamEnded))
            @if(StreamsHelper::getUserInProgressStream())
            <button class="btn btn-round btn-outline-danger btn-sm px-3 mb-0 d-flex align-items-center" onclick="Streams.showStreamEditDialog('create')">
                <div class="mr-1">{{__("Streaming")}}</div>
                <div>
                    <div class="blob red"></div>
                </div>
            </button>
            @endif
            @endif
        </div>
        <div class="p-3">
            @include('elements.streams.stream-details-banner')
        </div>
        <div class="px-3 pt-3">
            <div class="stream-wrapper row">
                <div class="stream-video col-12">
                    @if($stream->canWatchStream)
                    <video id="my_video_1" class="video-js vjs-fluid vjs-theme-forest" controls preload="auto" autoplay muted>
                        <source src="{{isset($streamEnded) ? 'https://'.$stream->vod_link : $stream->hls_link}}" type="application/x-mpegURL">
                    </video>
                    @else
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-12">
                            <div class="card p-5">
                                <div class="p-4 p-md-5">
                                    <img src="{{asset('/img/live-stream-locked.svg')}}" class="stream-locked">
                                </div>
                                <div class="d-flex align-items-center justify-content-center" style="">
                                    <span>🔒 {{__("Live stream requires a")}} @if(isset($subLocked)) {{__("valid")}}
                                        <a href="javascript:void(0);" class="stream-subscribe-label to-tooltip" @if(!GenericHelper::creatorCanEarnMoney($stream->user))
                                            data-placement="top"
                                            title="{{__('This creator cannot earn money yet')}}"
                                            @endif
                                            >{{__("user subscription")}}</a>@endif
                                        @if(isset($priceLocked))
                                        @if(isset($subLocked)){{__("and an")}}@endif <a href="javascript:void(0);" class="stream-unlock-label to-tooltip" @if(!GenericHelper::creatorCanEarnMoney($stream->user))
                                            data-placement="top"
                                            title="{{__('This creator cannot earn money yet')}}"
                                            @endif
                                            >{{__("one time fee")}}</a>
                                        @endif.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="pb-3">
                <div class="giftArea d-flex mt-4">
                    <p>
                        <button class="btn btn-primary mr-5" type="button" data-toggle="collapse" data-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                            Presentear
                        </button>
                    </p>
                    <div style="min-height: 120px;">
                        <div class="collapse width" id="collapseWidthExample">
                            <div class="card card-body" style="width: 620px;">
                                <div id="giftCarousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Joinha.png') }}" alt="Descrição da Imagem">
                                                        <h4>Joinha</h4>
                                                        <p class="text-muted">0,10 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Morango.png') }}" alt="Descrição da Imagem">
                                                        <h4>Morango</h4>
                                                        <p class="text-muted">0,25 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Pipoca.png') }}" alt="Descrição da Imagem">
                                                        <h4>Pipoca</h4>
                                                        <p class="text-muted">0,50 Snapcoins</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Beijo.png') }}" alt="Descrição da Imagem">
                                                        <h4>Beijo</h4>
                                                        <p class="text-muted">1,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Pêssego.png') }}" alt="Descrição da Imagem">
                                                        <h4>Pêssego</h4>
                                                        <p class="text-muted">10,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/COIN.png') }}" alt="Descrição da Imagem">
                                                        <h4>SuperCoin</h4>
                                                        <p class="text-muted">20,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Leite.png') }}" alt="Descrição da Imagem">
                                                        <h4>Leite</h4>
                                                        <p class="text-muted">50,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Coração.png') }}" alt="Descrição da Imagem">
                                                        <h4>Coração</h4>
                                                        <p class="text-muted">2,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Baú.png') }}" alt="Descrição da Imagem">
                                                        <h4>Baú</h4>
                                                        <p class="text-muted">75,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Pintinho.png') }}" alt="Descrição da Imagem">
                                                        <h4>Pintinho</h4>
                                                        <p class="text-muted">100,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Diabinho.png') }}" alt="Descrição da Imagem">
                                                        <h4>Diabinho</h4>
                                                        <p class="text-muted">200,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Rodquinha.png') }}" alt="Descrição da Imagem">
                                                        <h4>Rosquinha</h4>
                                                        <p class="text-muted">5,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/Sapatinho.png') }}" alt="Descrição da Imagem">
                                                        <h4>Sapatinho</h4>
                                                        <p class="text-muted">500,00 Snapcoins</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="giftText d-flex flex-column justify-content-center align-items-center p-4">
                                                        <img class="giftImage" src="{{ asset('img/PedidoEspecial.png') }}" alt="Descrição da Imagem">
                                                        <h4>Pedido Especial</h4>
                                                        <p class="text-muted">Criador define</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <a class="carousel-control-prev carouselBtnLeft" href="#giftCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next carouselBtnRight" href="#giftCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="">
                @include('elements.streams.stream-chat')
            </div>
        </div>


    </div>
</div>

@include('elements.checkout.checkout-box')
@include('elements.report-user-or-post',['reportStatuses' => ListsHelper::getReportTypes()])

@stop