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
                <form id="myForm" class="w-100">
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="formGroupExampleInput">Valor do conteúdo</label>
                        <input type="number" class="form-control" name="valor" id="formGroupExampleInput" placeholder="Definir valor da campanha" required>
                        <span id="errorPrice" style="display: none;" class="text-danger mt-2">Defina um valor para o anúncio </span>
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label>Adicionar arquivo</label>
                        <input id="inputFile" type="file" class="form-control @error('frontDoc') is-invalid @enderror uploadFb   " name="frontDoc" accept=".jpg, .jpeg, .png, .webp" required onchange="previewImage(this, document.getElementById('frontPreview'))">
                        <div>
                            <span id="errorFile" style="display: none;" class="text-danger mt-2">Por favor,selecione uma imagem</span>
                        </div>
                        <div class="preview">
                            <img id="frontPreview" src="#" alt="" style="display: none; max-height: 200px;">
                        </div>
                        @error('frontDoc')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label for="exampleFormControlTextarea1">Mensagem</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Mensagem de campanha" name="message" required></textarea>
                        <div>
                            <span id="MessageError" style="display: none; " class="text-danger mt-2"></span>
                        </div>
                    </div>
                    <div class="form-group p-3 ml-4 mr-4">
                        <label class="font-bold">Enviar campanha para</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="defaultCheck1" name="subscribers">
                            <p class="form-check-label" for="defaultCheck1">
                                Assinantes
                            </p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="defaultCheck2" name="followers">
                            <p class="form-check-label" for="defaultCheck2">
                                Seguidores
                            </p>
                        </div>
                        <div>
                            <span style="display: none;" id="errorGroup" class="text-danger">Selecione pelo menos um grupo para promover</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end w-100 mb-3 mt-3">
                        <div class="form-group btn-block ml-4 mr-4 p-3">
                            <button type="button" class="btn btn-block btn-primary btn-round post-create-button mb-0 uploadButton p-3">{{ __('Promover') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop


<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
    import {
        getStorage,
        ref,
        uploadBytes,
        getDownloadURL
    } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-storage.js";

    const firebaseConfig = {
        apiKey: "AIzaSyA0ztmepmqW28Lbrv1PU9E8dGnZhE0soWw",
        authDomain: "belinho-4a703.firebaseapp.com",
        databaseURL: "https://belinho-4a703-default-rtdb.firebaseio.com",
        projectId: "belinho-4a703",
        storageBucket: "belinho-4a703.appspot.com",
        messagingSenderId: "678978718971",
        appId: "1:678978718971:web:ba70e7d2e9031ce803745e"
    };

    const app = initializeApp(firebaseConfig);
    const storage = getStorage(app);

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

        // Adiciona o toast ao DOM
        const toastContainer = document.querySelector('.toast-container');
        if (toastContainer) {
            toastContainer.innerHTML = toastHTML;
            const toastElement = toastContainer.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    };

    btn.addEventListener('click', async () => {
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

        //ver se tem arquivo
        if (inputFile.files && inputFile.files[0]) {
            file = inputFile.files[0];
        } else {
            alert("Preencha todos os campos");
            return;
        }


        function generateNumericID(length) {
            let result = '';
            while (result.length < length) {
                result += Math.floor(Math.random() * 1000000000).toString();
            }
            return result.substring(0, length);
        }

        const UploadImage = async (file) => {
            try {
                const storageRef = ref(storage, `images/${file.name}`);
                await uploadBytes(storageRef, file);
                const url = await getDownloadURL(storageRef);
                return url;
            } catch (error) {
                throw new Error(`Erro durante o upload: ${error.message}`);
            }
        };

        try {

            imageUrl = await UploadImage(file);
        } catch (error) {
            alert(error);
            return;
        }
        try {
            const data = {
                price: parseFloat(valor),
                followers,
                message,
                subscribers,
                images: [{
                    attachmentID: generateNumericID(36),
                    path: imageUrl


                }]

            };

            const response = await fetch('/my/messenger/sendMessage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'


                },
                body: JSON.stringify(data)

            });

            if (response.ok) {
                const result = await response.json();
                console.log(result);
                launchToast("success", trans("Success"), `Campanha realizada com Sucesso`);
            } else {

                console.error('Erro ao enviar dados:', response.statusText);
                launchToast("danger", trans("Error"), `Erro ao realizar campanha. Tente novamente.`);
            }
        } catch (error) {
            console.error(error);
            launchToast("danger", trans("Error"), `Erro durante o upload ou envio. Tente novamente.`);
        }

        // Limpar campos
        inputFile.value = '';
        document.querySelector('input[name="valor"]').value = '';
        document.querySelector('input[name="followers"]').checked = false;
        document.querySelector('textarea[name="message"]').value = '';
        document.querySelector('input[name="subscribers"]').checked = false;
        document.querySelector('#MessageError').style.display = 'none';
        document.querySelector('#errorPrice').style.display = 'none';
        document.querySelector('#errorGroup').style.display = 'none';
    });
</script>