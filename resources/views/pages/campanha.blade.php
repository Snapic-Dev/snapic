@extends('layouts.user-no-nav')

@section('page_title', __('Campanha'))

@section('styles')
    {!! Minify::stylesheet(['/css/posts/post.css', '/libs/dropzone/dist/dropzone.css'])->withFullUrl() !!}
@stop

@section('scripts')
    {!! Minify::javascript(['/js/FirebaseUpload.js'])->withFullUrl() !!}
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            @include('elements.uploaded-file-preview-template')
            @include('elements.attachments-uploading-dialog')
            <div class="d-flex justify-content-between pt-4 pb-3 px-3 border-bottom">
                <h5
                    class="text-truncate text-bold ml-4 mr-4 p-3 {{ Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r') }}">
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
                        {{ __('Before being able to publish an item, you need to complete your') }} <a class="text-white"
                            href="{{ route('my.settings', ['type' => 'verify']) }}">{{ __('profile verification') }}</a>.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-column-reverse">
                    <form id="myForm" class="w-100" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group p-3 ml-4 mr-4">
                            <label for="formGroupExampleInput">Valor do conteúdo</label>
                            <input type="number" class="form-control valueCampaign" name="valor"
                                id="formGroupExampleInput" placeholder="Definir valor da campanha" min="10"
                                max="1000">
                        </div>
                        <div class="form-group p-3 ml-4 mr-4">
                            <label for="exampleFormControlTextarea1">Mensagem</label>
                            <textarea style="resize:none;" class="form-control" id="exampleFormControlTextarea1" maxlength="500" rows="5"
                                placeholder="Mensagem de campanha" name="message"></textarea>
                        </div>
                        <div class="form-group" style="padding: 0 40px;">
                            <label for="frontDoc" class="col-form-label">Adicionar arquivo</label>
                            <div class="file-uploadCampaign d-flex">
                                <input id="inputFile" type="file" class="form-control uploadFb" name="frontDoc"
                                    accept=".jpg, .jpeg, .png, .webp" title="Adicione um arquivo para prosseguir">
                                <button type="button" for="frontDoc" class="preview-img">
                                    <ion-icon name="folder-open-outline"></ion-icon>
                                    <span>{{ __('Escolher arquivo') }}</span>
                                </button>
                                <div class="preview">
                                    <img id="frontPreview" src="#" alt="Preview da CNH - Frente"
                                        style="display: none;">
                                </div>
                            </div>

                        </div>
                        <div class="form-group p-3 ml-4 mr-4">
                            <div class="d-flex gap-3">

                                <div class="checkbox-wrapper-12">
                                    <div class="cbx">
                                        <input checked="" class="form-check-input" type="checkbox" id="cbx-12"
                                            name="subscribers">
                                        <label for="cbx-12"></label>
                                        <svg fill="none" viewBox="0 0 15 14" height="14" width="15">
                                            <path d="M2 8.36364L6.23077 12L13 2"></path>
                                        </svg>
                                    </div>
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <filter id="goo-12">
                                                <feGaussianBlur result="blur" stdDeviation="4" in="SourceGraphic">
                                                </feGaussianBlur>
                                                <feColorMatrix result="goo-12"
                                                    values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 22 -7" mode="matrix"
                                                    in="blur"></feColorMatrix>
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
                                        <input checked="" class="form-check-input" type="checkbox" id="cbx-13"
                                            name="followers">
                                        <label for="cbx-13"></label>
                                        <svg fill="none" viewBox="0 0 15 14" height="14" width="15">
                                            <path d="M2 8.36364L6.23077 12L13 2"></path>
                                        </svg>
                                    </div>
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <filter id="goo-12">
                                                <feGaussianBlur result="blur" stdDeviation="4" in="SourceGraphic">
                                                </feGaussianBlur>
                                                <feColorMatrix result="goo-12"
                                                    values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 22 -7" mode="matrix"
                                                    in="blur"></feColorMatrix>
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
                                <span style="display: none;" id="errorGroup" class="text-danger mt-3">Selecione pelo
                                    menos um grupo para promover</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end w-100 mb-3 mt-3">
                            <div class="form-group btn-block ml-4 mr-4 p-3">
                                <button type="submit"
                                    class="btn btn-block btn-primary btn-round campaignBtn post-create-button mb-0 uploadButton p-3"
                                    disabled>
                                    <div class="d-flex justify-content-center spinnerArea">
                                        <span class="spinner spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <span id="buttonText">{{ __('Promover') }}</span>
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
    const SIX_HOURS = 21600;
    const loggedInUserID = '{{ Auth::user()->id }}';
    document.addEventListener('DOMContentLoaded', function() {
        const uploadButton = document.querySelector('.uploadButton');
        const storedTime = getCookie('campaignCooldown');
        const storedUserId = getCookie('campaignCooldownUser');

        if (storedTime && storedUserId === loggedInUserID) {
            const timeLeft = calculateTimeLeft(storedTime);
            if (timeLeft > 0) {
                console.log("AQ", timeLeft);
                startCooldown(timeLeft);
            }
        } else {
            uploadButton.removeAttribute('disabled');
            console.log("AQ", timeLeft);
        }
    });

    document.querySelector("#myForm").addEventListener('submit', async (e) => {
        e.preventDefault();
        const uploadButton = document.querySelector('.uploadButton');
        const spinner = document.querySelector('.spinner');
        const buttonText = document.getElementById('buttonText');

        uploadButton.removeAttribute('disabled');
        spinner.style.display = 'flex';
        buttonText.textContent = '{{ __('Enviando...') }}'; // Texto temporário enquanto envia

        try {
            const formData = new FormData(document.querySelector("#myForm"));
            formData.append('_token', '{{ csrf_token() }}');
            const response = await fetch('/my/messenger/sendMessage', {
                method: 'POST',
                body: formData,
            });

            if (response.ok) {
                const currentTime = Math.floor(Date.now() / 1000);
                setCookie('campaignCooldown', currentTime, 6);
                setCookie('campaignCooldownUser', loggedInUserID, 6);
                startCooldown(SIX_HOURS);
                showToast("Campanha realizada com sucesso!", false);
            } else {
                showToast("Erro ao enviar a campanha.", true);
            }
        } catch (error) {
            console.error('Erro ao enviar a campanha:', error);
            showToast("Erro ao enviar a campanha.", true);
        } finally {
            spinner.style.display = 'none';
        }
    });

    function startCooldown(seconds) {
        const uploadButton = document.querySelector('.uploadButton');
        const buttonText = document.getElementById('buttonText');
        uploadButton.disabled = true;
        let countdown = seconds;

        const interval = setInterval(() => {
            if (countdown > 0) {
                countdown--;
                const hours = Math.floor(countdown / 3600); // Horas
                const minutes = Math.floor((countdown % 3600) / 60); // Minutos
                const secondsLeft = countdown % 60; // Segundos restantes
                buttonText.textContent =
                    `Aguarde ${hours}:${minutes < 10 ? '0' : ''}${minutes}:${secondsLeft < 10 ? '0' : ''}${secondsLeft}`;
            } else {
                clearInterval(interval);
                buttonText.textContent = '{{ __('Promover') }}';
                uploadButton.disabled = false;
            }
        }, 1000);
    }

    function calculateTimeLeft(storedTime) {
        const currentTime = Math.floor(Date.now() / 1000);
        return SIX_HOURS - (currentTime - storedTime);
    }

    function setCookie(name, value, hours) {
        const date = new Date();
        date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/`;
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    document.querySelector("#inputFile").addEventListener('change', function() {
        const input = this;
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const bg = document.querySelector('.preview-img')
                bg.style.background =
                    `linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url(${e.target.result}) no-repeat center center/cover`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    const btn = document.querySelector('.uploadButton');
    const inputFile = document.querySelector('#inputFile');

    function showToast(message, isError = false) {
        const toastHTML = `
                <div class="toast ${isError ? 'bg-danger text-white' : 'bg-success text-white'}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">${isError ? 'Erro' : 'Sucesso'}</strong>
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
    }
</script>
