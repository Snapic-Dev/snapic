<h5 class="mt-3 p-2 text-bold text-md">{{__('Proceed with payment')}}</h5>
<div class="input-group mb-3 mt-3 p-2">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
    </div>
    <input class="form-control" placeholder="{{\App\Providers\PaymentsServiceProvider::getDepositLimitAmounts()}}" aria-label="{{__('Username')}}" aria-describedby="amount-label" id="deposit-amount" type="number" min="{{\App\Providers\PaymentsServiceProvider::getDepositMinimumAmount()}}" step="1" max="{{\App\Providers\PaymentsServiceProvider::getDepositMaximumAmount()}}">
    <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
</div>

<div>
    <div class="payment-method p-2">
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="pixRadio" name="payment-radio-option" class="custom-control-input" value="payment-oxxo" checked>
            <label class="pixRadioTxt custom-control-label stepTooltip text-bold" for="pixRadio" title="">Pix</label>
        </div>
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="boletoRadio" name="payment-radio-option" class="custom-control-input" value="payment-oxxo">
            <label class="custom-control-label stepTooltip text-opacity-field" for="" title="">Boleto</label>
        </div>
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="creditRadio" name="payment-radio-option" class="custom-control-input" value="payment-oxxo">
            <label class="creditRadioTxt custom-control-label stepTooltip" for="creditRadio" title="">Cartão</label>
        </div>

        <form class="flex-column form-group p-4 formInputCredit border mt-4" method="Post">
            <label class="text-sm mt-2" for="nome">
                <p class="text-medium">Número do cartão</p>
            </label>
            <input class="p-2 bg-transparent border" placeholder="Número do Cartão" type="text"></input>
            <label class="text-sm mt-4" for="nome" type="text">
                <p>Nome impresso no cartão</p>
            </label>
            <input class="p-2 bg-transparent border" placeholder="Nome impresso no cartão"></input>
            <div class="d-flex justify-between">
                <div class="mr-4">
                    <label class="text-sm mt-4" for="nome">
                        <p>Validade</p>
                    </label>
                    <input class="p-2 bg-transparent border" placeholder="Selecione uma data" type="date"></input>
                </div>
                <div class="ml-4">
                    <label class="text-sm mt-4" for="nome">
                        <p>Código de verificação</p>
                    </label>
                    <input class="p-2 bg-transparent border" placeholder="Código de verificação" type="text"></input>
                </div>
            </div>
            <div class="pb-2 pt-1">
                <div class="payment-error error text-danger d-none mt-3">{{__('Please select your payment method')}}</div>
                <button class="btn btn-round btn-primary btn-block mr-0 mt-4 p-3 deposit-continue-btn" type="submit">{{__('Add funds')}}</button>
            </div>
        </form>

        <div class="qrCodeArea border p-4 justify-content-center flex-column align-content-center">
            <div class="d-flex flex-column align-items-center">
                <label class="text-bold text-sm">Valor pix deposito</label>
                <input class="p-4 inputPix text-bold text-center" value="R$200,00"></input>
            </div>
            <div class="d-flex flex-column mt-2 pt-4 pl-5 pr-5 pb-3">
                <p class="text-bold text-sm">Instruções</p>
                <p class="text-sm">1. Copie o código PIX</p>
                <p class="text-sm">2. Abra o aplicativo do seu banco</p>
                <p class="text-sm">3. Entre na área <span class="text-bold">Pix Copia e Cola</span></p>
                <p class="text-sm">4. Cole o código e finalize a transação</p>
            </div>
            <div class="d-flex flex-column align-items-center">
                <button class="btnPix btn-round mb-3 p-3 d-flex">
                    <div class="ml-2">
                        @include('elements.icon',['icon'=>'cash-outline','variant'=>'small'])
                    </div>
                    Copiar código PIX
                </button>
                <button class="btnQr p-3 d-flex">
                    <div class="ml-2">
                        @include('elements.icon',['icon'=>'qr-code-outline','variant'=>'small'])
                    </div>
                    Gerar QR Code
                </button>
            </div>
        </div>


        <!-- @if(config('paypal.client_id') && config('paypal.secret'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio1" name="payment-radio-option" class="custom-control-input" value="payment-paypal">
            <label class="custom-control-label" for="customRadio1">{{__("Paypal")}}</label>
        </div>
        @endif
        @if(getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio2" name="payment-radio-option" class="custom-control-input" value="payment-stripe">
            <label class="custom-control-label stepTooltip" for="customRadio2" title="" data-original-title="{{__('You need to login first')}}">{{__("Stripe")}}</label>
        </div>
        @endif
        @if(getSetting('payments.coinbase_api_key'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio3" name="payment-radio-option" class="custom-control-input" value="payment-coinbase">
            <label class="custom-control-label stepTooltip" for="customRadio3" title="">{{__("Coinbase")}}</label>
        </div>
        @endif
        @if(getSetting('payments.nowpayments_api_key'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio5" name="payment-radio-option" class="custom-control-input" value="payment-nowpayments">
            <label class="custom-control-label stepTooltip" for="customRadio5" title="">{{__("NowPayments Crypto")}}</label>
        </div>
        @endif
        @if(getSetting('payments.mercado_access_token'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio6" name="payment-radio-option" class="custom-control-input" value="payment-mercado">
            <label class="custom-control-label stepTooltip" for="customRadio6" title="">{{__("MercadoPago")}}</label>
        </div>
        @endif
        @if(\App\Providers\PaymentsServiceProvider::ccbillCredentialsProvided())
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio6" name="payment-radio-option" class="custom-control-input" value="payment-ccbill">
            <label class="custom-control-label stepTooltip" for="customRadio6" title="">{{__("CCBill")}}</label>
        </div>
        @endif
        @if(getSetting('payments.paystack_secret_key'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio7" name="payment-radio-option" class="custom-control-input" value="payment-paystack">
            <label class="custom-control-label stepTooltip" for="customRadio7" title="">{{__("Paystack")}}</label>
        </div>
        @endif
        @if(getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key') && getSetting('payments.stripe_oxxo_provider_enabled'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio8" name="payment-radio-option" class="custom-control-input" value="payment-oxxo">
            <label class="custom-control-label stepTooltip" for="customRadio8" title="">{{__("Oxxo")}}</label>
        </div>
        @endif
        @if(getSetting('payments.allow_manual_payments'))
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio4" name="payment-radio-option" class="custom-control-input" value="payment-manual">
            <label class="custom-control-label stepTooltip" for="customRadio4" title="">{{__("Bank transfer")}}</label>
        </div>
        <div class="manual-details d-none">
            <h5 class="mt-4 mb-3">{{__("Add payment details")}}</h5>

            @if(getSetting('payments.offline_payments_iban'))
            <div class="alert alert-primary text-white font-weight-bold" role="alert">
                <p class="mb-0">{{__('Once confirmed, your credit will be available and you will be notified via email.')}}</p>
                <ul class="mt-2 mb-2">
                    <li>{{__('IBAN')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_iban')}}</span></li>
                    <li>{{__('BIC/SWIFT')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_swift')}}</span></li>
                    <li>{{__('Bank name')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_bank_name')}}</span></li>
                    <li>{{__('Account owner')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_owner')}}</span></li>
                    <li>{{__('Account number')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_account_number')}}</span></li>
                    <li>{{__('Routing number')}}: <span class="font-weight-bold">{{getSetting('payments.offline_payments_routing_number')}}</span></li>
                </ul>
            </div>
            @endif

            @if(getSetting('payments.offline_payments_custom_message_box'))
            <div class="alert alert-primary text-white font-weight-bold" role="alert">
                {!! getSetting('payments.offline_payments_custom_message_box') !!}
            </div>
            @endif

            <div>
                <label for="manualPaymentDescription" title="">{{__("Notes")}}</label>
                <textarea class="form-control" id="manualPaymentDescription" rows="1"></textarea>
                <span class="invalid-feedback" role="alert">
                    <strong>{{__("Payment notes are required")}}</strong>
                </span>
            </div>
            <p class="mb-1 mt-2">{{__("Please attach clear photos with one the following: check, money order or bank transfer.")}}</p>
            <div class="dropzone-previews dropzone manual-payment-uploader w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
            <small class="form-text text-muted mb-2">{{__("Allowed file types")}}: {{str_replace(',',', ',AttachmentHelper::filterExtensions('manualPayments'))}}.</small>
            <div class="text-danger invalid-files d-none">{{trans_choice('Please upload at least one file', (int)getSetting('payments.offline_payments_minimum_attachments_required'), ['num' => (int)getSetting('payments.offline_payments_minimum_attachments_required')])}}</div>
        </div>
        @endif
    </div> -->
    </div>
    @include('elements.uploaded-file-preview-template')

    <script>
        let creditRadio = document.getElementById("creditRadio");
        let pixRadio = document.getElementById("pixRadio");
        let formInputCredit = document.querySelector(".formInputCredit");
        let pixRadioTxt = document.querySelector(".pixRadioTxt");
        let creditRadioTxt = document.querySelector(".creditRadioTxt");

        let qrCodeArea = document.querySelector(".qrCodeArea");

        function showCreditInput() {
            if (creditRadio.checked) {
                formInputCredit.style.display = "flex";
                pixRadioTxt.style.fontWeight = "normal";
                creditRadioTxt.style.fontWeight = "bold";
                qrCodeArea.style.display = "none";
            } else {
                formInputCredit.style.display = "none";
                creditRadioTxt.style.fontWeight = "normal";
                pixRadioTxt.style.fontWeight = "bold";
                qrCodeArea.style.display = "flex";
            }
        }

        creditRadio.addEventListener("change", showCreditInput);
        pixRadio.addEventListener("change", showCreditInput);
    </script>