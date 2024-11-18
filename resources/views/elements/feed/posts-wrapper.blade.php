@if(count($posts))
    @foreach($posts as $post)
        @include('elements.feed.post-box')
        <hr>
    @endforeach
    @include('elements.report-user-or-post',['reportStatuses' => ListsHelper::getReportTypes()])
    @include('elements.feed.post-delete-dialog')
    @include('elements.feed.post-list-management')
    @include('elements.photoswipe-container')
@else
    <div class="d-flex justify-content-center align-items-center">
        <div class="col-10 d-flex align-items-center justify-content-center">
            <img  class="imgBannerNoContent"
            src="{{ asset(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '/img/nenhumaPostagemDisponivelWhite.svg' : '/img/nenhumaPostagemDisponivelBlack.svg') : (Cookie::get('app_theme') == 'dark' ? '/img/nenhumaPostagemDisponivelWhite.svg' : '/img/nenhumaPostagemDisponivelBlack.svg')) }}"
            >
        </div>
    </div>
    <!-- <div class="d-flex justify-content-center align-items-center">
        <h5 class="text-center mb-2 mt-2">{{__('No posts available')}}</h5>
    </div> -->
@endif
