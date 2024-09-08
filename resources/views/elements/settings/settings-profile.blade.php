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
            <div class="card profile-cover-bg">
                <img class="card-img-top centered-and-cropped" src="{{ Auth::user()->cover }}">
                <div class="card-img-overlay d-flex justify-content-center align-items-center">
                    <div class="actions-holder d-none">

                        <div class="d-flex">
                            <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Upload cover image') }}">
                                @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor"
                                onclick="ProfileSettings.removeUserAsset('cover')" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Remove cover image') }}">
                                @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card avatar-holder">
                <img class="card-img-top" src="{{ Auth::user()->avatar }}">
                <div class="card-img-overlay d-flex justify-content-center align-items-center">
                    <div class="actions-holder d-none">
                        <div class="d-flex">
                            <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Upload avatar') }}">
                                @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                            </span>
                            <span class="h-pill h-pill-accent pointer-cursor"
                                onclick="ProfileSettings.removeUserAsset('avatar')" data-toggle="tooltip"
                                data-placement="top" title="{{ __('Remove avatar') }}">
                                @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                            </span>
                        </div>

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
            aria-describedby="emailHelp" value="{{ Auth::user()->username }}">
        @if ($errors->has('username'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('username') }}</strong>
        </span>
        @endif

    </div>
    <div class="form-group p-2">
        <label for="name">{{ __('Full name') }}</label>
        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name"
            aria-describedby="emailHelp" value="{{ Auth::user()->name }}">
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
        <textarea class="form-control {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio"
            rows="3" spellcheck="false">{{ Auth::user()->bio }}</textarea>
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
                id="cpf" name="cpf" aria-describedby="emailHelp" value="{{ Auth::user()->cpf }}">
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
                id="phone" name="phone" aria-describedby="emailHelp" value="{{ Auth::user()->phone }}">
        </div>
        @if ($errors->has('phone'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('phone') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group px-2">
        <label for="niche" class="col-form-label">{{ __('Niche') }}</label>
        <div>
            <select id="niche" class="form-control @error('niche') is-invalid @enderror" name="niche" required>
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
        <button class="btn btn-round btn-primary btn-block mr-0" type="submit">{{ __('Save') }}</button>
    </div>
</form>