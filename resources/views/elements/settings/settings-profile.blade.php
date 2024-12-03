@if (!Auth::user()->email_verified_at)
@include('elements.resend-verification-email-box')
@endif

@if (getSetting('ai.open_ai_enabled'))
@include('elements.suggest-description')
@endif
<form method="POST" action="{{ route('my.settings.profile.save', ['type' => 'profile']) }}">
    @csrf
    @include('elements.dropzone-dummy-element')
    <div class="mb-4">
        <div class="">
            <div class="card profile-cover-bg bannerPerfil">
                    <img class="card-img-top centered-and-cropped bannerPerfilCrop" src="{{ Auth::user()->cover }}">
                    <div class="actions-holder d-none">
                        <div class="d-flex">
                            <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button uploadBanner" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Upload cover image') }}">
                                @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor removeBanner"
                                onclick="ProfileSettings.removeUserAsset('cover')" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Remove cover image') }}">
                                @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor cropBanner"
                                onclick="crop(bannerPerfilCrop)"
                                data-toggle="tooltip"
                                data-placement="top" title="{{ __('Dimensionar') }}">
                                @include('elements.icon', ['icon' => 'crop-outline', 'variant' => 'medium'])
                            </span>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade mt-5" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modelCrop">
                    <div class="modal-header border-0"">
                        <h5 class="modal-title p-2 titleCropModal text-bold" id="staticBackdropLabel">Editar Avatar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modalBodyCrop p-3">
                        <img class="cropImageModal" src="">
                        <div class="mt-4 d-flex">
                            <p class="text-bold mr-4">Zoom: <span class="zoomRangeValue text-base">x</span></p>
                            <p class="text-bold mr-4">Rotação: <span class="rotationRangeValue text-base">°</span></p>
                        </div>
                        <div class="">
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <div class="d-flex justify-center align-items-center">
                                    -
                                    <input class="zoomBanner ml-4 mr-4" type="range" min="1" max="3" step="0.1" value="1">
                                    +
                                </div>
                            </div>
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <div class="d-flex justify-center align-items-center">
                                    <div class="invert-icon">
                                        @include('elements.icon', ['icon' => 'reload-outline', 'variant' => 'small'])
                                    </div>
                                    <input class="RotateImage ml-4 mr-4" type="range"  min="-180" max="180" step="0.1" value="0">
                                    @include('elements.icon', ['icon' => 'reload-outline', 'variant' => 'small', 'class' => 'invert-icon'])
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center border-0 p-4">
                        <button type="button" class="btn btn-round border" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary saveCropBtn btn-round">Ok</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="card avatar-holder">
                <img class="card-img-top imgPefil" src="{{ Auth::user()->avatar }}">
                    <div class="actions-holder holderAvatar d-none">
                        <div class="d-flex">
                            <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button uploadAvatar" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Upload avatar') }}">
                                @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor removeButton"
                                onclick="ProfileSettings.removeUserAsset('avatar')" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Remove avatar') }}">
                                @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor cropAvatar"
                                onclick="crop(imgPefil)"
                                data-toggle="tooltip"
                                data-placement="top" title="{{ __('Dimensionar') }}">
                                @include('elements.icon', ['icon' => 'crop-outline', 'variant' => 'medium'])
                            </span>
                        </div>

                    </div>
                </div>
        </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="form-group p-2">
        <label for="username">{{ __('Username') }}</label>
        <input class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username"
            aria-describedby="emailHelp" value="{{ Auth::user()->username }}" maxlength=15 minlength=4>
        @if ($errors->has('username'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('username') }}</strong>
        </span>
        @endif

    </div>
    <div class="form-group p-2">
        <label for="name">{{ __('Full name') }}</label>
        <input 
            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
            id="name" 
            name="name" 
            aria-describedby="emailHelp" 
            value="{{ Auth::user()->name }}" 
            maxlength="40" 
            pattern="^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:\s+[A-Za-zÀ-ÖØ-öø-ÿ]+)+$" 
            title="Insira um nome válido" 
            required
        >
        @if ($errors->has('name'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group p-2">
        <div class="d-flex justify-content-between">
            <label for="bio">
                {{ __('Bio') }}
            </label>
            <div>
                @if (getSetting('ai.open_ai_enabled'))
                <a href="javascript:void(0)" onclick="{{ 'AiSuggestions.suggestDescriptionDialog();' }}"
                    data-toggle="tooltip" data-placement="left"
                    title="{{ __('Use AI to generate your description.') }}">{{ trans_choice('Suggestion', 2) }}</a>
                @endif
            </div>
        </div>
        <textarea style="resize:none;" maxlength="500" class="form-control {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio"
            rows="4" spellcheck="false">{{ Auth::user()->bio }}</textarea>
        @if ($errors->has('bio'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('bio') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group p-2">
        <label for="birthdate">{{ __('Birthdate') }}</label>
        <input type="date" class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="birthdate"
            name="birthdate" aria-describedby="emailHelp" value="{{ Auth::user()->birthdate }}"
            max="{{ $minBirthDate }}">
        @if ($errors->has('birthdate'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('birthdate') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group p-2">
        <label for="cpf" value="{{ Auth::user()->cpf }}">CPF</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control inputInstagram {{ $errors->has('cpf') ? 'is-invalid' : '' }}"
                pattern="^(?:\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$" id="cpf" name="cpf" aria-describedby="emailHelp" value="{{ Auth::user()->cpf }}" title="Adicione um CPF válido">
        </div>
        @if ($errors->has('cpf'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('cpf') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group p-2">
        <label for="phone" value="{{ Auth::user()->phone }}">Telefone</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control inputInstagram {{ $errors->has('phone') ? 'is-invalid' : '' }}"
            pattern="\d{2} \d{5}-\d{4}|\d{11}" 
            id="phone" name="phone" aria-describedby="emailHelp" value="{{ Auth::user()->phone }}" title="Adicione um número com ddd">
        </div>
        @if ($errors->has('phone'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('phone') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group p-2">
        <label for="niche" class="col-form-label">{{ __('Niche') }}</label>
        <div>
            <select id="niche" class="form-control @error('niche') is-invalid @enderror" name="niche">
                <option value="">{{ Auth::user()->niche ?? "Selecione um nicho" }}</option>
                @foreach ($niches as $niche)
                <option value="{{ $niche['name'] }}" {{ old('niche') == $niche['name'] ? 'selected' : '' }}>
                    {{ $niche['name'] }}
                </option>
                @endforeach
            </select>
            @error('niche')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="d-flex flex-row p-2">
        <div class="{{ getSetting('profiles.allow_gender_pronouns') ? 'w-50' : 'w-100' }} pr-2">
            <div class="form-group">
                <label for="gender">{{ __('Gender') }}</label>
                <select class="form-control" id="gender" name="gender">
                    <option value=""></option>
                    @foreach ($genders as $gender)
                    <option value="{{ $gender->id }}"
                        {{ Auth::user()->gender_id == $gender->id ? 'selected' : '' }}>
                        {{ __($gender->gender_name) }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('gender'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('gender') }}</strong>
                </span>
                @endif
            </div>
        </div>

        @if (getSetting('profiles.allow_gender_pronouns'))
        <div class="w-50 pl-2">
            <div class="form-group">
                <label for="pronoun">{{ __('Gender pronoun') }}</label>
                <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="pronoun"
                    name="pronoun" aria-describedby="emailHelp" value="{{ Auth::user()->gender_pronoun }}">
                @if ($errors->has('pronoun'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('pronoun') }}</strong>
                </span>
                @endif
            </div>
        </div>
        @endif

    </div>

    <div class="d-flex flex-row p-2">
        <div class="form-group w-50 pr-2 ">
            <label for="country">{{ __('Country') }}</label>
            <select class="form-control" id="country" name="country">
                <option value=""></option>
                @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ Auth::user()->country_id == $country->id ? 'selected' : '' }}>{{ __($country->name) }}
                </option>
                @endforeach
            </select>
            @if ($errors->has('country'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('country') }}</strong>
            </span>
            @endif
        </div>
        <div class="form-group w-50 pl-2">
            <label for="location">{{ __('Location') }}</label>
            <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location"
                name="location" aria-describedby="emailHelp" value="{{ Auth::user()->location }}">
            @if ($errors->has('location'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('location') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group p-2">
        <label for="website" value="{{ Auth::user()->website }}">Instagram</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    @include('elements.icon', ['icon' => 'logo-instagram', 'variant' => 'medium'])
                </span>
            </div>
            <input type="url" class="form-control inputInstagram {{ $errors->has('website') ? 'is-invalid' : '' }}"
                pattern="https://(www\.)?instagram\.com/[a-zA-Z0-9_]+" id="website" name="website" aria-describedby="emailHelp" value="{{ Auth::user()->website }}" title="A URL deve ser um perfil válido do Instagram">
        </div>
        @if ($errors->has('website'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('website') }}</strong>
        </span>
        @endif
    </div>
    <div class="p-2 pb-4 pt-4">
        <button class="p-3 btn btn-round btn-primary btn-block mr-0" type="submit">{{ __('Save') }}</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.js"></script>

<script>
    let bannerPerfilCrop=document.querySelector('.bannerPerfilCrop')
    let imgPefil=document.querySelector('.imgPefil');
    let uploadAvatar=document.querySelector('.uploadAvatar');
    let removeButton=document.querySelector('.removeButton');
    let uploadBanner=document.querySelector('.uploadBanner');
    let removeBanner=document.querySelector('.removeBanner');
    let cropBanner=document.querySelector('.cropBanner');
    let cropAvatar=document.querySelector('.cropAvatar');
    let cropper;
    let croppedImage;
    let zoomBanner=document.querySelector('.zoomBanner');
    let zoomOutBanner=document.querySelector('.zoomOutBanner');
    let saveBanner=document.querySelector('.saveBanner');
    let saveAvatar=document.querySelector('.saveAvatar');
    let cancelAvatarCrop=document.querySelector('.cancelAvatarCrop');
    let cancelBannerCrop=document.querySelector('.cancelBannerCrop');
    let cropImageModal=document.querySelector('.cropImageModal');
    let saveCropBtn=document.querySelector('.saveCropBtn');

    let cropBox = document.querySelector('.cropper-crop-box');
    let titleCropModal =document.querySelector('.titleCropModal');
    let elementToCrop=document.querySelector('.elementToCrop');
    let zoomRangeValue=document.querySelector('.zoomRangeValue');
    let rotationRangeValue=document.querySelector('.rotationRangeValue')



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

    let cropViewMode;

    function crop(elementCrop) {

        if (cropper) {
            cropper.destroy();
        }

        if(elementCrop.classList.contains("bannerPerfilCrop")) {
            let imgBanner=bannerPerfilCrop.getAttribute('src')
            cropImageModal.src=imgBanner

            titleCropModal.innerText="Editar Banner"

            saveCropBtn.setAttribute('onclick',"saveCrop(bannerPerfilCrop)")
            cropViewMode=2;

        } else if(elementCrop.classList.contains("imgPefil")) {
            let imgPerfil=imgPefil.getAttribute('src')
            cropImageModal.src=imgPerfil

             titleCropModal.innerText="Editar Avatar"

            saveCropBtn.setAttribute('onclick',"saveCrop(imgPefil)")
            cropViewMode=1;

        }


        setTimeout(function() {

            let originalWidth = elementCrop.naturalWidth;
            let originalHeight = elementCrop.naturalHeight;

            let aspectRatio = originalWidth / originalHeight;
            console.log(`aspectRatio ${aspectRatio}`);

            cropper = new Cropper(cropImageModal, {
                aspectRatio: aspectRatio,
                viewMode: `${cropViewMode}`,
                zoomable: true,
                autoCropArea: 1,
                scalable: false,
                background: false,
                cropBoxResizable: true, 
                cropBoxMovable: true,
                quality: 1,
                ready: function () {
                    const cropBox = this.cropper.cropBox;
                    if(elementCrop.classList.contains("imgPefil")) {
                        cropBox.style.borderRadius = '50%';  // Deixa a área de corte circular
                        cropBox.style.overflow = 'hidden';   // Garante que a área cortada se ajuste ao círculo
                    }else if(elementCrop.classList.contains("bannerPerfilCrop")) {
                        cropBox.style.borderRadius = '0%';  // Deixa a área de corte circular
                    }
                }
            });

        }, 300);

        
        $('#staticBackdrop').modal('show');
    }


    function saveCrop(imageElement) {
        if(imageElement && cropper) {

            croppedImage=cropper.getCroppedCanvas().toDataURL("image/png");
            imageElement.setAttribute('src',croppedImage)

            let formData = new FormData();
            function base64ToFile(base64, filename) {
            const arr = base64.split(',');
            const mime = arr[0].match(/:(.*?);/)[1]; 
            const binary = atob(arr[1]); 
            const len = binary.length;
            const u8arr = new Uint8Array(len);

            for (let i = 0; i < len; i++) {
                u8arr[i] = binary.charCodeAt(i);
            }

            return new File([u8arr], filename, { type: mime });
        }

            let croppedImageFile = base64ToFile(croppedImage, 'cropped-image.png');
            formData.append('file', croppedImageFile);
            formData.append('_token', '{{ csrf_token() }}');

            let uploadType = imageElement.classList.contains('bannerPerfilCrop') ? 'cover' : 'avatar';

            fetch("/my/settings/profile/upload/" + uploadType, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    launchToast("success", trans("Success"),'Imagem Redimensionada com sucesso!');
                } else {
                    launchToast("danger", trans("Error"), 'Erro no redimensionamento da imagem.Tente novamente!');
                }
            })
            .catch(error => {
                launchToast("danger", trans("Error"), 'Erro interno no redimensionamento da imagem.Tente novamente!');
                console.log(error);
            });

            cropper.destroy();
        }

        $('#staticBackdrop').modal('hide');
    }

    let zoomAtual = 1;
    zoomRangeValue.innerText = zoomBanner.value + 'x';

    zoomBanner.addEventListener('input', function() {
        let zoomValue = parseFloat(zoomBanner.value);  
        zoomRangeValue.innerText = zoomValue + 'x';

        if (zoomValue < 1) {
        zoomValue = 1;
        } else if (zoomValue > 3) {
            zoomValue = 3;
        }

        if (zoomAtual > zoomValue) {
            zoomOut(); 
        } else if (zoomAtual < zoomValue) {
            zoomIn(); 
        }
    });

    function zoomOut() {

        let newZoom = zoomAtual - 0.1;
        if (newZoom >= 1) { 
            zoomAtual = newZoom;
            cropper.zoom(-0.1);  
        }
    }

    function zoomIn() {

        let newZoom = zoomAtual + 0.1;
        if (newZoom <= 3) {  
            zoomAtual = newZoom;
            cropper.zoom(0.1); 
        }
    }

    const rotateInput = document.querySelector('.RotateImage');

    rotationRangeValue.innerText= rotateInput.value + '°';

    rotateInput.addEventListener('input', function() {
        let rotationValue = parseFloat(rotateInput.value); 
        rotationRangeValue.innerText= rotationValue + '°'; 

        cropper.rotateTo(rotationValue); 
        
    });




</script>