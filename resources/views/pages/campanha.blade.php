@extends('layouts.user-no-nav')

@section('page_title', __('Campanha'))

@section('styles')
{!! Minify::stylesheet(['/css/posts/post.css', '/libs/dropzone/dist/dropzone.css'])->withFullUrl() !!}
@stop

@section('scripts')
<script type="module" src="{{ asset('js/FirebaseUpload.js') }}"></script>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        @include('elements.uploaded-file-preview-template')
        @include('elements.attachments-uploading-dialog')
        <div class="d-flex justify-content-between pt-4 pb-3 px-3 border-bottom">
            <h5 class="text-truncate text-bold ml-4 mr-4 p-3 {{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r') }}">
                Campanha
            </h5>
        </div>
        @if (!PostsHelper::getDefaultPostStatus(Auth::user()->id))
        <div class="pl-3 pr-3 pt-3">
            @include('elements.pending-posts-warning-box')
        </div>
        @endif
        <div class="pl-3 pr-3 pt-2">
            @if (!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))
            <div class="alert alert-warning text-white font-weight-bold mt-2 mb-0" role="alert">
                {{ __('Before being able to publish an item, you need to complete your') }} <a class="text-white" href="{{ route('my.settings', ['type' => 'verify']) }}">{{ __('profile verification') }}</a>.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="d-flex flex-column-reverse">
                <form id="myForm" class="w-100">
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="formGroupExampleInput">Valor do conteúdo</label>
                        <input type="number" class="form-control" name="valor" id="formGroupExampleInput" placeholder="Definir valor do anúncio" required>
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="exampleFormControlTextarea1">Mensagem</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Mensagem de campanha" name="message" required></textarea>
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="frontDoc" class="col-form-label">Anexar imagem</label>
                        <input id="frontDoc" type="file" class="form-control @error('frontDoc') is-invalid @enderror" name="frontDoc" accept=".jpg, .jpeg, .png, .webp" required onchange="previewImage(this, document.getElementById('frontPreview'))">
                        <div class="preview">
                            <img id="frontPreview" src="#" alt="Preview da CNH - Frente" style="display: none; max-height: 200px;">
                        </div>
                        @error('frontDoc')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="p-3 ml-4 mr-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="defaultCheck1" name="subscribers">
                            <label class="form-check-label" for="defaultCheck1">
                                Assinantes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="defaultCheck2" name="followers">
                            <label class="form-check-label" for="defaultCheck2">
                                Seguidores
                            </label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between w-100 mb-3 mt-3 p-3">
                        <div class="form-group btn-block ml-4 mr-4">
                            <button type="button" onclick="onSubmit()" id="uploadButton" class="btn btn-primary btn-block btn-round post-create-button mb-0 p-3">{{ __('Promover') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>
<script>
    import {
        UploadImage
    } from "/js/FirebaseUpload.js"

    async function onSubmit() {
        // upload da imagem
        const url = await UploadImage(document.querySelector(".file"))

        //log de todos inputs + url
        console.log(url);

        //fetch para controller
        const res = fetch("url", {
            url: url,
            price,
            message,
            followers,
            subscribers
        })


        //dar resposta para o usuario
        res.json

    }
</script>