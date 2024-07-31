<h5 class="mt-4 p-2 text-bold text-md">{{ __('Proceed with payment') }}</h5>
<div class="input-group mb-3 mt-3 p-2">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon', ['icon' => 'cash-outline', 'variant' => 'medium'])</span>
    </div>
    <input class="form-control inputText depositInput"
        placeholder="{{ \App\Providers\PaymentsServiceProvider::getDepositLimitAmounts() }}"
        aria-label="{{ __('Username') }}" aria-describedby="amount-label" id="deposit-amount" type="number"
        min="{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}" step="1"
        max="{{ \App\Providers\PaymentsServiceProvider::getDepositMaximumAmount() }}">
    <div class="invalid-feedback">{{ __('Please enter a valid amount.') }}</div>
</div>
<div class="feedbackForUser text-sm mb-3 ml-2 text-bold"></div>

<div>
    <div class="payment-method p-2">
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="pixRadio" name="payment-radio-option" class="custom-control-input"
                value="payment-oxxo" checked>
            <label class="pixRadioTxt custom-control-label stepTooltip text-bold" for="pixRadio"
                title="">Pix</label>
        </div>
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="boletoRadio" name="payment-radio-option" class="custom-control-input"
                value="payment-oxxo">
            <label class="custom-control-label stepTooltip text-opacity-field" for=""
                title="">Boleto</label>
        </div>
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="creditRadio" name="payment-radio-option" class="custom-control-input"
                value="payment-oxxo">
            <label class="creditRadioTxt custom-control-label stepTooltip" for="creditRadio"
                title="">Cartão</label>
        </div>

        <!-- Button trigger modal -->
        <div class="mt-4">
            <!-- Modal -->
            <div class="modal fade show" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title p-2 text-bold" id="staticBackdropLabel">Pix</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="pixBox p-5 justify-content-center flex-column align-content-center">
                                <div class="d-flex flex-column align-items-center">
                                    <!-- <input class="p-4 inputPix text-bold text-center" disabled></input> -->
                                    <div class="amountText d-flex justify-content-between p-2">
                                        <h4>Pagamento Total</h4>
                                        <h5 class="amountPix"></h5>
                                    </div>
                                    <div class="line"></div>
                                    <div class="timePayment d-flex justify-content-between p-2">
                                        <h4>Pagar em até</h4>
                                        <h5 id="dataExpiration" aria-placeholder="00h 00min 00s"></h5>
                                    </div>
                                    <div class="p-4 mt-3 mb-2">
                                        <img class="qrCodeImage" src=""></img>
                                    </div>
                                </div>
                                <div class="inputPixArea mt-5 mb-5 p-2">
                                    <label class="textInputArea">Copie o código abaixo</label>
                                    <input class="inputPixCode" value="dsjbasdbubdsibsdifasjiiasfj@dkndfsnon"></input>
                                </div>
                                <div class="instructionPix flex-column mt-2 pt-4 pb-3 text-center">
                                    <h5 class="p-2 text-bold">Código PIX gerado com sucesso</h5>
                                    <p class="p-2 text-sm text-muted">Use o aplicativo de seu banco para ler o QRCode ao
                                        lado,
                                        ou toque no botão PIX Copia e Cola para copiar o código
                                        e realizar a transação no aplicativo do seu banco
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-center mt-3">
                                    <button class="btnPix btn btn-round mb-3 p-3 d-flex" onclick="copyCodePix()">
                                        <div class="ml-4">
                                            @include('elements.icon', [
                                                'icon' => 'cash-outline',
                                                'variant' => 'small',
                                            ])
                                        </div>
                                        Copiar código PIX
                                    </button>
                                    <button class="btnQr btn btn-round btn-secundary border p-3 d-flex">
                                        <div class="ml-4">
                                            @include('elements.icon', [
                                                'icon' => 'qr-code-outline',
                                                'variant' => 'small',
                                            ])
                                        </div>
                                        Gerar QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div> -->
                    </div>
                </div>
                <div class="ml-4">
                    <label class="text-sm mt-4" for="nome">
                        <p>Código de verificação</p>
                    </label>
                    <input class="p-2 bg-transparent border" placeholder="Código de verificação" type="text"></input>
                </div>
            </div>
            <div class="pb-2 pt-1">
                <div class="payment-error error text-danger d-none mt-3">{{ __('Please select your payment method') }}
                </div>
                <button class="btn btn-round btn-primary btn-block mr-0 mt-4 p-3 deposit-continue-btn"
                    type="submit">{{ __('Add funds') }}</button>
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
                            @include('elements.icon', ['icon' => 'cash-outline', 'variant' => 'small'])
                        </div>
                        Copiar código PIX
                    </button>
                    <button class="btnQr p-3 d-flex">
                        <div class="ml-2">
                            @include('elements.icon', ['icon' => 'qr-code-outline', 'variant' => 'small'])
                        </div>
                        Gerar QR Code
                    </button>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" onclick="generatePix()"
                    class="modalCreditCard btn-block btn-round btn border btn-primary p-3"
                    data-target="#staticBackdrop" onclick="showDepositValue()" <span
                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
                    <div class="d-flex justify-content-center">
                        <span class="spinner spinner-border spinner-border-sm mr-2" role="status"
                            aria-hidden="true"></span>
                        <span class="textLoadingBtn">Depositar</span>
                    </div>
                </button>
                <div class="modal fade" id="staticBackdrop2" data-backdrop="static" data-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title creditModalTitle" id="staticBackdropLabel">Adicione cartão de
                                    crédito ou débito</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-row p-4">
                                        <div class="col-7 mt-2">
                                            <label class="text-sm text-bold">Nome Cartão</label>
                                            <input type="text" class="form-control" placeholder="Nome Cartão">
                                        </div>
                                        <div class="col mt-2">
                                            <label class="text-sm text-bold">Validade</label>
                                            <input type="text" class="form-control" placeholder="MM/YY">
                                        </div>
                                        <div class="col mt-2">
                                            <label class="text-sm text-bold">CVV</label>
                                            <input type="text" class="form-control" placeholder="CVV">
                                        </div>
                                    </div>
                                    <div class="form-row p-4">
                                        <div class="col mt-2">
                                            <label class="text-sm text-bold">Número Cartão</label>
                                            <input type="text" class="form-control"
                                                placeholder="0000 0000 0000 0000">
                                        </div>
                                    </div>
                                </form>
                                <p class="p-2 text-sm text-muted ml-3">
                                    Seus dados de cartão estão seguros conosco. Preencha os campos com confiança para
                                    concluir sua transação com segurança.
                                </p>
                                <button class="p-3 pl-2 pr-2 mt-3 mb-3 btn btn-round btn-primary border btn-block">
                                    Confirmar
                                </button>
                            </div>
                            <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- @if (config('paypal.client_id') && config('paypal.secret'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio1" name="payment-radio-option" class="custom-control-input" value="payment-paypal">
            <label class="custom-control-label" for="customRadio1">{{ __('Paypal') }}</label>
        </div>
@endif
        @if (getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio2" name="payment-radio-option" class="custom-control-input" value="payment-stripe">
            <label class="custom-control-label stepTooltip" for="customRadio2" title="" data-original-title="{{ __('You need to login first') }}">{{ __('Stripe') }}</label>
        </div>
@endif
        @if (getSetting('payments.coinbase_api_key'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio3" name="payment-radio-option" class="custom-control-input" value="payment-coinbase">
            <label class="custom-control-label stepTooltip" for="customRadio3" title="">{{ __('Coinbase') }}</label>
        </div>
@endif
        @if (getSetting('payments.nowpayments_api_key'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio5" name="payment-radio-option" class="custom-control-input" value="payment-nowpayments">
            <label class="custom-control-label stepTooltip" for="customRadio5" title="">{{ __('NowPayments Crypto') }}</label>
        </div>
@endif
        @if (getSetting('payments.mercado_access_token'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio6" name="payment-radio-option" class="custom-control-input" value="payment-mercado">
            <label class="custom-control-label stepTooltip" for="customRadio6" title="">{{ __('MercadoPago') }}</label>
        </div>
@endif
        @if (\App\Providers\PaymentsServiceProvider::ccbillCredentialsProvided())
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio6" name="payment-radio-option" class="custom-control-input" value="payment-ccbill">
            <label class="custom-control-label stepTooltip" for="customRadio6" title="">{{ __('CCBill') }}</label>
        </div>
@endif
        @if (getSetting('payments.paystack_secret_key'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio7" name="payment-radio-option" class="custom-control-input" value="payment-paystack">
            <label class="custom-control-label stepTooltip" for="customRadio7" title="">{{ __('Paystack') }}</label>
        </div>
@endif
        @if (getSetting('payments.stripe_secret_key') &&
                getSetting('payments.stripe_public_key') &&
                getSetting('payments.stripe_oxxo_provider_enabled'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio8" name="payment-radio-option" class="custom-control-input" value="payment-oxxo">
            <label class="custom-control-label stepTooltip" for="customRadio8" title="">{{ __('Oxxo') }}</label>
        </div>
@endif
        @if (getSetting('payments.allow_manual_payments'))
<div class="custom-control custom-radio mb-1">
            <input type="radio" id="customRadio4" name="payment-radio-option" class="custom-control-input" value="payment-manual">
            <label class="custom-control-label stepTooltip" for="customRadio4" title="">{{ __('Bank transfer') }}</label>
        </div>
        <div class="manual-details d-none">
            <h5 class="mt-4 mb-3">{{ __('Add payment details') }}</h5>

            @if (getSetting('payments.offline_payments_iban'))
<div class="alert alert-primary text-white font-weight-bold" role="alert">
                <p class="mb-0">{{ __('Once confirmed, your credit will be available and you will be notified via email.') }}</p>
                <ul class="mt-2 mb-2">
                    <li>{{ __('IBAN') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_iban') }}</span></li>
                    <li>{{ __('BIC/SWIFT') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_swift') }}</span></li>
                    <li>{{ __('Bank name') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_bank_name') }}</span></li>
                    <li>{{ __('Account owner') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_owner') }}</span></li>
                    <li>{{ __('Account number') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_account_number') }}</span></li>
                    <li>{{ __('Routing number') }}: <span class="font-weight-bold">{{ getSetting('payments.offline_payments_routing_number') }}</span></li>
                </ul>
            </div>
@endif

            @if (getSetting('payments.offline_payments_custom_message_box'))
<div class="alert alert-primary text-white font-weight-bold" role="alert">
                {!! getSetting('payments.offline_payments_custom_message_box') !!}
            </div>
@endif

            <div>
                <label for="manualPaymentDescription" title="">{{ __('Notes') }}</label>
                <textarea class="form-control" id="manualPaymentDescription" rows="1"></textarea>
                <span class="invalid-feedback" role="alert">
                    <strong>{{ __('Payment notes are required') }}</strong>
                </span>
            </div>
            <p class="mb-1 mt-2">{{ __('Please attach clear photos with one the following: check, money order or bank transfer.') }}</p>
            <div class="dropzone-previews dropzone manual-payment-uploader w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
            <small class="form-text text-muted mb-2">{{ __('Allowed file types') }}: {{ str_replace(',', ', ', AttachmentHelper::filterExtensions('manualPayments')) }}.</small>
            <div class="text-danger invalid-files d-none">{{ trans_choice('Please upload at least one file', (int) getSetting('payments.offline_payments_minimum_attachments_required'), ['num' => (int) getSetting('payments.offline_payments_minimum_attachments_required')]) }}</div>
        </div>
@endif
    </div> -->
        </div>
        @include('elements.uploaded-file-preview-template')

        <script>
            let pixRadioTxt = document.querySelector(".pixRadioTxt");
            let creditRadioTxt = document.querySelector(".creditRadioTxt");
            let modalCreditCard = document.querySelector(".modalCreditCard");
            let depositInput = document.querySelector(".depositInput");
            let modalPix = document.querySelector("#staticBackdrop");
            let qrCodeImage = document.querySelector(".qrCodeImage");
            let amountPix = document.querySelector(".amountPix");
            let btnPix = document.querySelector(".btnPix");
            let qrcodeLoading = document.querySelector(".qrcodeLoading");
            let textLoadingBtn = document.querySelector(".textLoadingBtn");
            let spinner = document.querySelector('.spinner')
            let feedbackForUser = document.querySelector('.feedbackForUser');

            function showCreditInput() {
                if (pixRadio.checked) {
                    modalCreditCard.setAttribute("data-target", "#staticBackdrop")
                    pixRadioTxt.style.fontWeight = "bold";
                    creditRadioTxt.style.fontWeight = "normal";
                } else {
                    modalCreditCard.setAttribute("data-target", "#staticBackdrop2")
                    creditRadioTxt.style.fontWeight = "bold";
                    pixRadioTxt.style.fontWeight = "normal";
                }
            }

            creditRadio.addEventListener("change", showCreditInput);
            pixRadio.addEventListener("change", showCreditInput);

            let pixCode;

            function showDepositValue() {
                let valueDeposit = depositInput.value;
                let formattedDeposit = valueDeposit.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
                inputPix.value = "R$" + formattedDeposit;
            }

            const hostname = window.location.origin;
            const url = `${hostname}/payment/pix`;

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

            function showSpinner() {
                spinner.style.display = 'flex';
                console.log('spinner')
            }

            function hideSpinner() {
                spinner.style.display = 'none';
            }


            const generatePix = async () => {
                feedbackForUser.innerText = ""
                if (depositInput.value !== "" && depositInput.value > 50) {
                    if (modalCreditCard.getAttribute("data-target") === "#staticBackdrop") {
                        amountPix.innerText = ""
                        showSpinner();
                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    username: "example",
                                    transaction_type: "deposit",
                                    provider: "pix",
                                    amount: depositInput.value
                                }),
                            });

                            if (!response.ok) {
                                launchToast("danger", trans("Error"),
                                    `Network response was not ok ${response.statusText}`);
                                return;
                            }

                            const responseData = await response.json();
                            console.log(responseData.id);
                            const qrCode = await responseData.qr_codes[0];
                            console.log(qrCode);


                            const expiration_date = qrCode.expiration_date;


                            calculateTimeDifference(expiration_date);


                            if (responseData) {
                                qrCodeImage.setAttribute('src', qrCode.links[0].href);
                                amountPix.innerText = "R$" + depositInput.value;
                                $('#staticBackdrop').modal('show')
                                launchToast("success",
                                    trans("Success"), "Pix gerado com sucesso");
                            }

                            pixCode = qrCode.text;
                        } catch (error) {
                            console.log(error);
                            launchToast("danger", trans("Error"), "Erro inesperado");
                        } finally {
                            hideSpinner();
                        }
                    } else {
                        showSpinner();
                        $('#staticBackdrop2').modal('show')
                    }
                } else {
                    feedbackForUser.innerText = "Preencha o campo para prosseguir"
                }
            }

            function copyCodePix() {
                navigator.clipboard.writeText(pixCode)
                    .then(() => {
                        sessionStorage.setItem('valorCopiado', pixCode);
                        btnPix.innerHTML = `<div class="ml-4">
                                            @include('elements.icon', [
                                                'icon' => 'cash-outline',
                                                'variant' => 'small',
                                            ])
                                        </div>
                                        Código copiado`;
                        setTimeout(() => {
                            btnPix.innerHTML = `<div class="ml-4">
                                            @include('elements.icon', [
                                                'icon' => 'cash-outline',
                                                'variant' => 'small',
                                            ])
                                        </div>
                                         Copiar código PIX`;
                        }, 1500);
                    })
                    .catch(err => {
                        console.error('Erro ao copiar: ', err);
                    });
            }

            function calculateTimeDifference(dataExpiration) {
                const expiration = new Date(dataExpiration);
                const expirationElement = document.getElementById('dataExpiration');
                let intervalId;

                const updateRemainingTime = () => {
                    const now = new Date();
                    let differenceInMilliseconds = expiration.getTime() - now.getTime();
                    if (differenceInMilliseconds < 0) {
                        clearInterval(intervalId);
                        return "00:00:00";
                    }

                    const differenceInSeconds = Math.floor(differenceInMilliseconds / 1000);
                    const hours = Math.floor(differenceInSeconds / 3600);
                    const minutes = Math.floor((differenceInSeconds % 3600) / 60);
                    const seconds = differenceInSeconds % 60;

                    const formattedHours = String(hours).padStart(2, '0');
                    const formattedMinutes = String(minutes).padStart(2, '0');
                    const formattedSeconds = String(seconds).padStart(2, '0');

                    expirationElement.innerText = `${formattedHours}h ${formattedMinutes}min ${formattedSeconds}s`;
                };

                intervalId = setInterval(updateRemainingTime, 1000);
                updateRemainingTime();
            }
        </script>
        intervalId = setInterval(updateRemainingTime, 1000);
        updateRemainingTime();
        }
        </script>
