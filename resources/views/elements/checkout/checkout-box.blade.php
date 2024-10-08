<div class="row checkout-dialog">
    <div class="col-lg-6 mx-auto">
        {{-- Paypal and stripe actual buttons --}}
        <div class="paymentOption paymentPP d-none">
            <form id="pp-buyItem" method="post" action="{{route('payment.initiatePayment')}}">
                @csrf
                <input type="hidden" name="amount" id="payment-deposit-amount" value="">
                <input type="hidden" name="transaction_type" id="payment-type" value="">
                <input type="hidden" name="post_id" id="post" value="">
                <input type="hidden" name="user_message_id" id="userMessage" value="">
                <input type="hidden" name="recipient_user_id" id="recipient" value="">
                <input type="hidden" name="provider" id="provider" value="">
                <input type="hidden" name="first_name" id="paymentFirstName" value="">
                <input type="hidden" name="last_name" id="paymentLastName" value="">
                <input type="hidden" name="billing_address" id="paymentBillingAddress" value="">
                <input type="hidden" name="city" id="paymentCity" value="">
                <input type="hidden" name="state" id="paymentState" value="">
                <input type="hidden" name="postcode" id="paymentPostcode" value="">
                <input type="hidden" name="country" id="paymentCountry" value="">
                <input type="hidden" name="taxes" id="paymentTaxes" value="">
                <input type="hidden" name="stream" id="stream" value="">
                <input type="hidden" name="card_token" id="cardToken" value="">
                <input type="hidden" name="cpf" id="cpf" value="">
                <input type="hidden" name="name" id="name" value="{{ Auth::user()->name }}">
                <button class="payment-button" type="submit"></button>
            </form>
        </div>

        <div class="paymentOption ml-2 paymentStripe d-none">
            <button id="stripe-checkout-button">{{__('Checkout')}}</button>
        </div>

        <!-- Modal -->
        <div class="checkout-popup modal fade" id="checkout-center" tabindex="-1" role="dialog" aria-labelledby="checkout" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payment-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="payment-body">
                            <div class="d-flex flex-row p-2">
                                <div class="ml-0 ml-md-2 mb-2">
                                    <img src="" class="rounded-circle user-avatar">
                                </div>
                                <div class="d-lg-block">
                                    <div class="pl-2 d-flex justify-content-center flex-column">
                                        <div class="ml-2 ">
                                            <div class="text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}} name"></div>
                                            <div class="text-muted username"><span>@</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-description mb-3 d-none p-2"></div>
                                <div class="input-group mb-3 checkout-amount-input d-none p-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="amount-label">
                                            @include('elements.icon',['icon'=>'cash-outline','variant'=>'medium','centered'=>false])
                                        </span>
                                    </div>
                                    <input class="form-control uifield-amount" 
                                    placeholder="Insira o valor que deseja enviar" aria-label="Username" aria-describedby="amount-label" id="checkout-amount" type="number" min="1">
                                    <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
                                </div>
                                <div class="mb-3 checkout-cpf-input d-none p-2">
                                    <label for="checkout-cpf"><b>CPF</b><span style="color:blueviolet">*</span></label>
                                    <input class="form-control uifield-amount cpf_input" required placeholder="000.000.000-00" aria-label="CPF" aria-describedby="cpf-label" id="checkout-cpf" type="text">
                                </div>
                        </div>
                        <div class="mb-3 p-2">
                            <div class="total row">
                                <span class="col-sm left"><b>{{__('Total')}}:</b></span>
                                <span class="total-amount col-sm right text-right">
                                    <b>$0.00</b>
                                </span>
                            </div>
                        </div>

                        <div id="accordion" class="mb-3 card-form d-none p-2">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between" id="headingOne" data-toggle="collapse" data-target="#billingInformation" aria-expanded="true" aria-controls="billingInformation">
                                    <h6 class="mb-0">
                                        {{__('Card information')}}
                                    </h6>
                                    <div class="ml-1 label-icon">
                                        @include('elements.icon',['icon'=>'chevron-down-outline','centered'=>false])
                                    </div>
                                </div>
                                <div id="billingInformation" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                        <form id="billing-agreement-form" onsubmit="handleFormSubmit(event)">
                                            <div class="tab-content">
                                                <!-- credit card info-->
                                                <div id="individual" class="tab-pane fade show active pt-1">
                                                    <div class="form-group">
                                                        <label for="cpf">
                                                            <span>CPF</span>
                                                        </label>
                                                        <input class="form-control cpf_input" placeholder="000.000.000-00" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="billingCity">
                                                            <span>{{__('Card Number')}}</span>
                                                        </label>
                                                        <input maxlength="19" class="form-control cardNumber" placeholder="0000 0000 0000 0000" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="billingPostcode">
                                                                    <span>{{__('Validate')}}</span>
                                                                </label>
                                                                <input type="text" class="form-control cardDateValidate" id="data" name="data" placeholder="MM/YYYY" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="billingState">
                                                                    <span>CVV</span>
                                                                </label>
                                                                <input type="text" class="form-control cardCVV" placeholder="CVV" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="billing-agreement-error error text-danger d-none">{{__('Please complete all billing details')}}</div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="choose-method p-2">
                            <h6 class="mb-4 ">{{__('Payment method')}}</h6>
                            <div class="radio-buttons">
                                <label class="radio-button credit-payment-provider show-card-form" data-value="credit">
                                    <input type="radio" id="credit_input" name="payment_type" value="credit" class="radio" checked>
                                    <div class="radio-circle"></div>
                                    <b class="textRadioCheckout">{{ ucfirst(__("wallet")) }}</b>
                                    <div class="available-credit ml-1">R$ {{ number_format(floatval(Auth::user()->wallet->total), 2, ',', '.') }}</div>
                                </label>
                                <label class="radio-button card-payment-provider show-card-form" data-value="card">
                                    <input type="radio" name="payment_type" value="card" class="radio">
                                    <div class="radio-circle"></div>
                                    <span class="radio-label textRadioCheckout">{{ ucfirst(__("Card")) }}</span>
                                </label>
                                <label class="radio-button pix-payment-provider show-card-form" data-value="pix">
                                    <input type="radio" name="payment_type" value="pix" class="radio">
                                    <div class="radio-circle"></div>
                                    <span class="radio-label textRadioCheckout">Pix</span>
                                </label>
                            </div>
                        </div>
                        <div class="payment-error error text-danger text-bold d-none mb-1">{{__('Please select your payment method')}}</div>
                        <p class="text-muted mt-1 p-2"> {{__('Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website.')}} </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">{{__('Cancel')}}</button>
                        <button type="submit" class="btn btn-round btn-primary checkout-continue-btn">{{__('Continue')}}
                            <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
                                <span class="sr-only">{{__('Loading...')}}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="checkout-popup-pix modal fade" id="checkout-pix" tabindex="-1" role="dialog" aria-labelledby="checkout" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title p-2 text-bold titlePixModal" id="staticBackdropLabel">Pix gerado com sucesso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pixModalBody">
                        <div class="pixBox p-5 justify-content-center flex-column align-content-center">
                            <div class="d-flex flex-column align-items-center">
                                <!-- <input class="p-4 inputPix text-bold text-center" disabled></input> -->
                                <div class="amountText d-flex justify-content-between p-2">
                                    <h4>Pagamento Total</h4>
                                    <h5 id="amountPix"></h5>
                                </div>
                                <div class="line"></div>
                                <div class="timePayment d-flex justify-content-between p-2">
                                    <h4>Pagar em até</h4>
                                    <h5 id="dataExpiration" aria-placeholder="00h 00min 00s"></h5>
                                </div>
                                <div class="p-4 mt-3 mb-2">
                                    <div id="qrcode"></div>
                                </div>
                            </div>
                            <div class="inputPixArea mt-5 mb-5 p-2">
                                <label class="textInputArea">Copie o código abaixo</label>
                                <input class="inputPixCode" value="dsjbasdbubdsibsdifasjiiasfj@dkndfsnon"></input>
                            </div>
                            <div class="instructionPix flex-column mt-2 pt-4 pb-3 text-center">
                                <p class="p-2 text-sm text-muted textInstruction">Use o aplicativo de seu banco para ler o QRCode ao
                                    lado,
                                    ou toque no botão PIX Copia e Cola para copiar o código
                                    e realizar a transação no aplicativo do seu banco
                                </p>
                            </div>
                            <div class="d-flex flex-column align-items-center mt-3">
                                <button class="btnPix btn btn-round mb-3 p-3 d-flex" data-value="" onclick="copyCodePix(this)">
                                    <div class="ml-4">
                                        @include('elements.icon', [
                                        'icon' => 'cash-outline',
                                        'variant' => 'small',
                                        ])
                                    </div>
                                    Copiar código PIX
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
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdeli vr.net/gh/efipay/js-payment-token-efi/dist/payment-token-efi-umd.min.js"></script>
<script>
    const showCard = document.querySelectorAll('.show-card-form')
    const formSubmit = document.querySelector('.checkout-continue-btn')
    const paymentTypeInput = document.querySelector('#payment-type');
    const cardForm = document.querySelector('.card-form')
    const cpf_input = document.querySelector('.checkout-cpf-input')

    document.addEventListener("DOMContentLoaded", function() {
        let creditInput = document.querySelector('#credit_input');
        if (creditInput) {
            creditInput.checked = true;
        }
    });

    showCard.forEach((div) => {
        div.addEventListener("click", () => {
            const dataValue = div.getAttribute('data-value');
            if (dataValue === 'card') {
                cardForm.classList.remove('d-none');
                cpf_input.classList.add('d-none');
            } else if (dataValue === 'credit') {
                cardForm.classList.add('d-none');
                formSubmit.removeAttribute('disabled');
                cpf_input.classList.add('d-none');
            } else {
                formSubmit.removeAttribute('disabled');
                cardForm.classList.add('d-none');
                if ("{{!Auth::user()->cpf}}")
                    cpf_input.classList.remove('d-none')
            }
        });
    });

    document.querySelector('.cardDateValidate').addEventListener('input', (event) => {
        const currentYear = new Date().getFullYear();

        let value = event.target.value.replace(/\D/g, '');

        if (value.length >= 2) {
            let month = value.substring(0, 2);

            if (Number(month) > 12 || Number(month) < 1) {
                launchToast("danger", trans("Error"), "Mês inválido. O mês deve estar entre 01 e 12.");
                event.target.value = '';
                return;
            }

            if (value.length > 2) {
                let year = value.substring(2, 6);

                if (year.length === 4 && Number(year) < currentYear || year.length === 4 && Number(year) >
                    currentYear + 10) {
                    launchToast("danger", trans("Error"),
                        `Ano inválido. O ano deve estar entre ${currentYear} e ${currentYear + 10}.`);
                    event.target.value = '';
                    return;
                }

                value = `${month}/${year}`;
            } else {
                value = month;
            }
        }

        event.target.value = value;
    });

    document.querySelector('.cardCVV').addEventListener('input', (event) => {
        let value = event.target.value.replace(/\D/g, '');
        if (value.length > 3) {
            launchToast("danger", trans("Error"), "O código CVV deve conter 3 dígitos.");
            value = ''
        }
        event.target.value = value;
    })

    document.querySelectorAll('.cpf_input').forEach((element) => {

        element.addEventListener('input', (event) => {
            let value = event.target.value.replace(/\D/g, ''); // Remove todos os caracteres que não são dígitos

            if (value.length > 11) {
                value = value.slice(0, 11); // Apenas mantém os primeiros 11 dígitos
            }

            value = value.replace(/(\d{3})(\d)/, '$1.$2'); // Primeiro ponto
            value = value.replace(/(\d{3})(\d)/, '$1.$2'); // Segundo ponto
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); // Traço antes dos dois últimos dígitos

            event.target.value = value;
            document.querySelector("#cpf").value = value
        });
    })

    document.querySelector('.cardNumber').addEventListener('input', (event) => {
        let value = event.target.value.replace(/\D/g, '');
        value = value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        event.target.value = value;
    });


    function copyCodePix(element) {
        const pixCopiaECola = element.getAttribute('data-value');

        navigator.clipboard.writeText(pixCopiaECola)
            .then(() => {
                launchToast("success", trans("Success"), "Pix copiado para área de transferência");
            })
            .catch(err => {
                launchToast("danger", trans("Error"), "Erro ao copiar pix");
            });
    }
</script>