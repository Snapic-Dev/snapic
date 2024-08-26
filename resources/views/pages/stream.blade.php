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
                        <button class="btn d-flex btn-primary btn-round align-content-center justify-content-center" type="button" data-toggle="collapse" data-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                            <div class="d-flex btnGiftInternArea">
                                @include('elements.icon', [
                                'icon' => 'gift',
                                'variant' => 'medium',
                                ])
                                <h5>Presentear</h5>
                            </div>
                        </button>
                    </p>
                    <div>
                        <div class="collapse width" id="collapseWidthExample">
                            <div class="card card-body" style="width: 620px;">
                                <div id="giftCarousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner" id="gifts-container">

                                    </div>
                                    <a class="carousel-control-prev" href="#giftCarousel" role="button" data-slide="prev">
                                        <h2 class="carouselArrowLeft"><</h2>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#giftCarousel" role="button" data-slide="next">
                                        <h2 class="carouselArrowRight">></h2>
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

<script>
   document.addEventListener('DOMContentLoaded', async function() {
    try {

        const fetchGifts = async () => {

            const response = await fetch('/giftDates');


            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        };

        const gifts = await fetchGifts();
        console.log(gifts);

        const container = document.getElementById('gifts-container');
        container.innerHTML = '';


        const chunkArray = (array, size) => {
            const result = [];
            for (let i = 0; i < array.length; i += size) {
                result.push(array.slice(i, i + size));
            }
            return result;
        };

        const giftChunks = chunkArray(gifts, 3);

        giftChunks.forEach((chunk, index) => {
            const giftElement = document.createElement('div');
            giftElement.classList.add('carousel-item');
            const giftAdjust = document.createElement('div');
            giftAdjust.classList.add('giftsArea');
            giftElement.appendChild(giftAdjust);

            if (index === 0) {
                giftElement.classList.add('active');
            }

            chunk.forEach(gift => {
                const giftInnerElement = document.createElement('div');
                giftInnerElement.classList.add('gift-item');
                giftInnerElement.innerHTML = `
                    <button class="giftText d-flex flex-column justify-content-center align-items-center p-4" onclick="giftForInfluencer(${gift.id})">
                        <img class="giftImage" src="${gift.imagem}" alt="Descrição da Imagem">
                        <div class="mt-2">
                            <h4>${gift.name}</h4>
                            <p>R$ ${gift.value}</p>
                        </div>
                    </button>
                `;
                giftAdjust.appendChild(giftInnerElement);
            });

            container.appendChild(giftElement);
        });
    } catch (error) {
        console.error('Error fetching gifts:', error);
    }
});

    const showToast = (message, isError = false) => {
        const toastHTML = `
                <div class="toast ${isError ? 'bg-danger text-white' : 'bg-success text-white'}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">${isError ? 'Error' : 'Success'}</strong>
                        <small>Agora</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;

        // Adiciona o toast ao DOM
        const toastContainer = document.querySelector('.toast-container');
        if (toastContainer) {
            toastContainer.innerHTML = toastHTML;
            const toastElement = toastContainer.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    };

    async function giftForInfluencer(idGift) {
        const url = '/process-gift';

        const path = window.location.pathname;
        const streamId = path.split('/')[2]

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    IdStream: streamId,
                    giftId: idGift
                })
            });

            const res = await response.json();
            console.log(res);
            if (res.status === 'success') {
                launchToast("success", trans("Success"), res.message);
            } else {
                launchToast("danger", trans("Error"), res.message);
            }
        } catch (error) {
            launchToast("danger", trans("Error"), res.message);
        }
    }

</script>