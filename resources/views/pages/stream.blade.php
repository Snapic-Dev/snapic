@extends('layouts.user-no-nav')
@section('page_title', $stream->name)

@section('styles')
<link rel="stylesheet" href="{{asset('/libs/video.js/dist/video-js.min.css')}}">
<link rel="stylesheet" href="{{asset('/css/player-theme.css')}}">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
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
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> <!-- Script do Swiper -->
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
            @if($stream->user->id !== Auth::user()->id)
            <div>
                <div class="giftArea d-flex mt-1">
                    <div style="width: 100%;">
                        <div class="collapse width d-flex" id="collapseWidthExample">
                            <div class="card card-body d-flex">
                                <div class="swiper-button-next"><i class="bi bi-caret-right-fill"></i></div>
                                <div class="swiper-button-prev"><i class="bi bi-caret-left-fill"></i></div>
                                <div class="swiper-container">
                                    <div class="swiper-wrapper" id="gifts-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
            const container = document.getElementById('gifts-container');
            container.innerHTML = '';

            gifts.forEach(gift => {
                const giftElement = document.createElement('div');
                giftElement.classList.add('swiper-slide');
                giftElement.innerHTML = `
                    <div class="giftText d-flex flex-column justify-content-center align-items-center" onclick="giftForInfluencer(${gift.id})" lazy="true">
                        <div>
                            <img class="giftImage" src="${gift.imagem}" alt="Descrição da Imagem" loading="lazy>
                        </div>
                        <div class="mt-2">
                            <h4>${gift.name}</h4>
                            <p>R$ ${gift.value.toFixed(2)}</p>
                        </div>
                    </div>
                `;
                container.appendChild(giftElement);
            });

            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 10,
                spaceBetween: 85,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
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
            if (res.status === 'success') {
                launchToast("success", trans("Success"), res.message);
                Stream.appendCommentToStreamChat(res.dataHtml);
                Stream.updateChatNoCommentsLabel();
                Stream.resetTextAreaHeight();

                // const wallets = document.querySelectorAll('.wallet-total-amount');

                // wallets.forEach(wallet => {
                //     let creditText = wallet.innerHTML.split("$")[1];
                //     let credit = parseFloat(creditText);
                //     let value = parseFloat(res.value)
                //     wallet.innerHTML = `R$${(credit - value).toFixed(2)}`;
                // });

            } else {
                launchToast("danger", trans("Error"), res.message);
            }
        } catch (error) {
            launchToast("danger", trans("Error"), res.message);
        }
    }
</script>