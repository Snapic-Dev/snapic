<div class="modal" tabindex="-1" id="checkoutModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title p-2">Termos de uso e Privacidade</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mt-3 mb-3 ml-2">
            Para continuar utilizando o <strong>Snapic</strong>, pedimos que você revise e aceite nossos <a class="text-bold"
            href="{{ route('pages.get', ['slug' => GenericHelper::getTOSPage()->slug]) }}">{{ __('Terms of Use') }}</a>. Certifique-se de ler atentamente as condições, pois elas regem o uso da nossa plataforma.
        </p>
        <div class="form-group p-2">
            <div class="custom-control custom-checkbox mt-3 mb-3">
                <div class="">
                    <input class="custom-control-input @error('terms') is-invalid @enderror" id="tosAgree" type="checkbox"
                        name="terms" value="1" placeholder="{{ __('Terms and Conditions') }}">
                    <label class="custom-control-label px-3" for="tosAgree">
                        <span>{{ __('I agree to the') }} <a
                                href="{{ route('pages.get', ['slug' => GenericHelper::getTOSPage()->slug]) }}">{{ __('Terms of Use') }}</a>
                            {{ __('and') }} <a
                                href="{{ route('pages.get', ['slug' => GenericHelper::getPrivacyPage()->slug]) }}">{{ __('Privacy Policy') }}</a>.</span>
                    </label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-round">Confirmar</button>
      </div>
    </div>
  </div>
</div>