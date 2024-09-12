@extends('layouts.user-no-nav')

@section('page_title', __('Campanha'))

@section('styles')
{!! Minify::stylesheet(['/css/posts/post.css', '/libs/dropzone/dist/dropzone.css'])->withFullUrl() !!}
@stop

@section('scripts')
{!! Minify::javascript([
'/js/FirebaseUpload.js',
])->withFullUrl() !!}
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
                <form id="myForm" class="w-100" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="formGroupExampleInput" class="required-label">Valor do conteúdo</label>
                        <input type="number" class="form-control valueCampaign" name="valor" id="formGroupExampleInput" placeholder="Definir valor da campanha" min="1" required>
                        <span id="errorPrice" style="display: none;" class="text-danger mt-2">Defina um valor para o anúncio </span>
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="exampleFormControlTextarea1" class="required-label">Mensagem</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Mensagem de campanha" name="message" required></textarea>
                        <div>
                            <span id="MessageError" style="display: none; " class="text-danger mt-2"></span>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 0 40px;">
                        <label for="frontDoc" class="col-form-label required-label">Adicionar arquivo</label>
                        <div class="file-uploadCampaign">
                            <input id="inputFile" type="file" class="form-control @error('frontDoc') is-invalid @enderror uploadFb required-label" name="frontDoc" accept=".jpg, .jpeg, .png, .webp" required>
                            <button for="frontDoc" class="">
                            <ion-icon name="folder-open-outline"></ion-icon>
                                <span>{{ __('Escolher arquivo') }}</span>
                            </button>
                            <div class="preview">
                                <img id="frontPreview" src="#" alt="Preview da CNH - Frente" style="display: none;">
                            </div>
                        </div>

                        <div>
                            <span id="errorFile" style="display: none;" class="text-danger mt-2">Por favor,selecione uma imagem</span>
                        </div>
                        @error('frontDoc')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <div class="d-flex gap-3">
                            <div class="checkbox-wrapper-12">
                                <div class="cbx">
                                    <input checked="" class="form-check-input" type="checkbox" id="cbx-12" name="subscribers">
                                    <label for="cbx-12"></label>
                                    <svg fill="none" viewBox="0 0 15 14" height="14" width="15">
                                        <path d="M2 8.36364L6.23077 12L13 2"></path>
                                    </svg>
                                </div>
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <filter id="goo-12">
                                            <feGaussianBlur result="blur" stdDeviation="4" in="SourceGraphic"></feGaussianBlur>
                                            <feColorMatrix result="goo-12" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 22 -7" mode="matrix" in="blur"></feColorMatrix>
                                            <feBlend in2="goo-12" in="SourceGraphic"></feBlend>
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                            <div class="ml-2">
                                <label for="cbx-12">Assinantes</label>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-2">
                            <div class="checkbox-wrapper-12">
                                <div class="cbx">
                                    <input checked="" class="form-check-input" type="checkbox" id="cbx-13" name="followers">
                                    <label for="cbx-13"></label>
                                    <svg fill="none" viewBox="0 0 15 14" height="14" width="15">
                                        <path d="M2 8.36364L6.23077 12L13 2"></path>
                                    </svg>
                                </div>
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <filter id="goo-12">
                                            <feGaussianBlur result="blur" stdDeviation="4" in="SourceGraphic"></feGaussianBlur>
                                            <feColorMatrix result="goo-12" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 22 -7" mode="matrix" in="blur"></feColorMatrix>
                                            <feBlend in2="goo-12" in="SourceGraphic"></feBlend>
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                            <div class="ml-2">
                                <label for="cbx-13">Seguidores</label>
                            </div>
                        </div>
                        <div>
                            <span style="display: none;" id="errorGroup" class="text-danger">Selecione pelo menos um grupo para promover</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end w-100 mb-3 mt-3">
                        <div class="form-group btn-block ml-4 mr-4 p-3">
                            <button type="submit" class="btn btn-block btn-primary btn-round campaignBtn post-create-button mb-0 uploadButton p-3">
                                <div class="d-flex justify-content-center spinnerArea">
                                    <span class="spinner spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span class="textLoadingBtn">{{ __('Promover') }}</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop


<script type="module">

    document.querySelector("#inputFile").addEventListener('change', function() {
        const input = this;
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewElement = document.querySelector('#frontPreview');
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            const previewElement = document.querySelector('#frontPreview');
            previewElement.src = '#';
            previewElement.style.display = 'none';
        }
    });

    const btn = document.querySelector('.uploadButton');
    const inputFile = document.querySelector('#inputFile');

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

    document.querySelector("#myForm").addEventListener('submit', async (e) => {
        e.preventDefault();
        const valor = document.querySelector('input[name="valor"]').value;
        const followers = document.querySelector('input[name="followers"]').checked;
        const message = document.querySelector('textarea[name="message"]').value;
        const subscribers = document.querySelector('input[name="subscribers"]').checked;
        const MessageError = document.querySelector('#MessageError');
        const errorPrice = document.querySelector('#errorPrice');
        const errorGroup = document.querySelector('#errorGroup');

        function Error(element, message) {
            element.textContent = message;
            element.style.display = 'block';
        }

        function hiddenError(element) {
            element.style.display = 'none';
        }

        if (valor.length < 1) {
            Error(errorPrice, 'Defina um valor para sua campanha.');
            return;
        } else {
            hiddenError(errorPrice);
        }

        if (message.length < 10) {
            Error(MessageError, 'A mensagem deve ter pelo menos 10 caracteres.');
            return;
        } else {
            hiddenError(MessageError);
        }

        if (!followers && !subscribers) {
            Error(errorGroup, 'Você deve selecionar pelo menos um grupo.');
            return;
        } else {
            hiddenError(errorGroup);
        }

        let imageUrl = '';
        let file;


        if (!inputFile.files || !inputFile.files[0]) {
            alert("Preencha todos os campos");
            return;
        }


        function showSpinner() {
            spinner.style.display = 'flex';
        }

        function hideSpinner() {
            spinner.style.display = 'none';
        }
    
        const spinner =document.querySelector('.spinner')
        const uploadButton=document.querySelector('.uploadButton')

        function generateNumericID(length) {
            let result = '';
            while (result.length < length) {
                result += Math.floor(Math.random() * 1000000000).toString();
            }
            return result.substring(0, length);
        }
        try {
            uploadButton.disabled=true;
            showSpinner();
            const formData = new FormData();
            formData.append('price', valor);
            formData.append('followers', followers);
            formData.append('message', message);
            formData.append('subscribers', subscribers);
            formData.append('image', inputFile.files[0]);

            const response = await fetch('/my/messenger/sendMessage', {
                method: 'POST',
                body: formData

            });


            if (response.ok) {
                const result = await response.json();
                launchToast("success", trans("Success"), `Campanha realizada com Sucesso`);
                spinner.style.display = 'none';
                uploadButton.disabled=false;
            } else {
                console.error('Erro ao enviar dados:', response.statusText);
                launchToast("danger", trans("Error"), `Erro ao realizar campanha. Tente novamente.`);
                spinner.style.display = 'none';
                uploadButton.disabled=false;
            }
        } catch (error) {
            console.error(error);
            launchToast("danger", trans("Error"), `Erro durante o upload ou envio. Tente novamente.`);
            spinner.style.display = 'none';
            uploadButton.disabled=false;
        }

        // Limpar campos
        inputFile.value = '';
        document.querySelector('input[name="valor"]').value = '';
        document.querySelector('input[name="followers"]').checked = false;
        document.querySelector('textarea[name="message"]').value = '';
        document.querySelector('textarea[name="message"]').value = '';
        document.querySelector('input[name="subscribers"]').checked = false;
        document.querySelector('#MessageError').style.display = 'none';
        document.querySelector('#errorPrice').style.display = 'none';
        document.querySelector('#errorGroup').style.display = 'none';

        const frontPreview = document.querySelector('#frontPreview');
        frontPreview.style.display = 'none';


    });




</script>