<div class="input-group mb-3 mt-5 p-2 d-block">
    <div class="">
        <label class="text-sm text-bold">Valor do Depósito</label>
        <input class="form-control inputText depositInput"
            placeholder="{{ \App\Providers\PaymentsServiceProvider::getDepositLimitAmounts() }}"
            aria-label="{{ __('Username') }}" aria-describedby="amount-label" id="deposit-amount" type="number"
            min="{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}" step="1"
            max="{{ \App\Providers\PaymentsServiceProvider::getDepositMaximumAmount() }}">
        <div class="invalid-feedback">{{ __('Please enter a valid amount.') }}</div>
        <!-- <div class="p-1">
            <label class="text-sm text-muted">Valor mínimo de R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00 para saque</label>
        </div> -->
    </div>
    <div class="ml-2 d-flex justify-content-center mt-5">
        <button class="btnOne btn btn-round border ml-2"
            onclick="inputDepositValueBtn(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00
        </button>
        @php
        $minAmount = \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount();
        $increments = [2, 5, 10, 20, 50]; // Fatores de incremento progressivo
        $depositValues = [];

        $baseAmount = ceil($minAmount / 10) * 10;

        foreach ($increments as $increment) {
        $nextValue = $baseAmount + ($increment * 10);
        $depositValues[] = $nextValue;
        }
        @endphp

        @foreach ($depositValues as $value)
        <button class="btn btn-round border ml-2"
            onclick="inputDepositValueBtn('{{ $value }}')">
            R${{ $value }},00
        </button>
        @endforeach
    </div>
</div>
<div>
    <div class="payment-method p-2">
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="pixRadio" name="payment-radio-option" class="custom-control-input"
                value="pix">
            <label class="custom-control-label stepTooltip text-bold" for="pixRadio"
                title="">Pix</label>
        </div>
        <!-- <div class="custom-control custom-radio mb-1">
            <input type="radio" id="boletoRadio" name="payment-radio-option" class="custom-control-input"
                value="payment-oxxo">
            <label class="custom-control-label stepTooltip text-opacity-field" for=""
                title="">Boleto</label>
        </div> -->
        <div class="custom-control custom-radio mb-1">
            <input type="radio" id="creditRadio" name="payment-radio-option" class="custom-control-input"
                value="card">
            <label class="custom-control-label text-bold stepTooltip" for="creditRadio"
                title="">Cartão</label>
        </div>

        <!-- Button trigger modal -->
        <div class="mt-4">
            <!-- Modal -->
            <div class="modal fade show" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
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

        <div class="mt-4">
            <button type="button" onclick="deposit()"
                class="modalCreditCard btnDeposit btn-block btn-round btn border btn-primary p-3"
                data-target="#staticBackdrop" onclick="showDepositValue()"
                class="spinner-border spinner-border-sm" role="status" aria-hidden="true" disabled>
                <div class="d-flex justify-content-center">
                    <span class="spinner spinner-border spinner-border-sm mr-2" role="status"
                        aria-hidden="true"></span>
                    <span class="textLoadingBtn">Depositar</span>
                </div>
            </button>
            <div class="p-3 pb-4 mt-4">
                <p class="text-xs alertWitdrawalMsg">
                    <strong>Aviso Importante:</strong>
                    Restrição de Idade para Depósitos
                    Por favor, esteja ciente de que depósitos só podem ser realizados por indivíduos maiores de
                    <strong>18 anos</strong>. Qualquer tentativa de depósito por <strong>menores de idade</strong> será
                    rejeitada conforme nossa <strong>política de segurança</strong>.
                </p>
            </div>
            <div class="modal fade" id="staticBackdrop2" data-backdrop="static" data-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title p-2" id="staticBackdropLabel">Adicione cartão de
                                crédito ou débito</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-row p-4">
                                    <div class="col mt-2">
                                        <label class="text-sm text-bold">Validade</label>
                                        <input type="month" class="form-control cardDateValidate" id="data" name="data" placeholder="MM/YY">
                                    </div>
                                    <div class="col mt-2">
                                        <label class="text-sm text-bold">CVV</label>
                                        <input type="number" class="form-control cardCVV" placeholder="CVV">
                                    </div>
                                </div>
                                <div class="form-row p-4">
                                    <div class="col mt-2">
                                        <label class="text-sm text-bold">Número Cartão</label>
                                        <input maxlength="19" class="form-control cardNumber" placeholder="0000 0000 0000 0000">
                                    </div>
                                </div>
                            </form>
                            <p class="p-2 text-sm text-muted ml-3 mt-2 mb-4">
                                Seus dados de cartão estão seguros conosco. Preencha os campos com confiança para
                                concluir sua transação com segurança.
                            </p>
                            <button type="button" onclick="deposit()"
                                class="modalCreditCard btnDeposit btn-block btn-round btn border btn-primary p-3"
                                data-target="#staticBackdrop2"
                                class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
                                <div class="d-flex justify-content-center">
                                    <span class="spinner spinner-border spinner-border-sm mr-2" role="status"
                                        aria-hidden="true"></span>
                                    <span class="textLoadingBtn">Depositar</span>
                                </div>
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

    </div>
    @include('elements.uploaded-file-preview-template')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/efipay/js-payment-token-efi/dist/payment-token-efi-umd.min.js"></script>
    <script>
        let pixRadio = document.querySelector("#pixRadio");
        let modalCreditCard = document.querySelector(".modalCreditCard");
        let modalPix = document.querySelector("#staticBackdrop");
        let textLoadingBtn = document.querySelector(".textLoadingBtn");
        let spinner = document.querySelector('.spinner')
        let dataExpiration = document.getElementById('dataExpiration');
        let depositInput = document.querySelector('.depositInput')
        let btnDeposit = document.querySelector('.btnDeposit');
        let cardNumber = document.querySelector('.cardNumber')
        let cardDateValidate = document.querySelector('.cardDateValidate')
        let cardCVV = document.querySelector('.cardCVV')

        document.addEventListener("DOMContentLoaded", function() {
            if (pixRadio) {
                pixRadio.checked = true;
            }
            showCreditInput();
        });

        let pixCopiaECola;

        function inputDepositValueBtn(depositValue) {
            depositInput.value = depositValue;
            inputDepositValue();
        };

        function inputDepositValue() {
            let inputValue = depositInput.value
            const minimumDepositAmount = parseFloat('{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}');

            if (depositInput.value >= minimumDepositAmount) {
                btnDeposit.disabled = false;
            } else {
                btnDeposit.disabled = true;
            }
        };

        depositInput.addEventListener('input', inputDepositValue)

        function toggleButtonSubmit() {
            if (pixRadio.checked) {
                textLoadingBtn.innerText = "Depositar";
                btnDeposit.onclick = deposit;
                modalCreditCard.setAttribute("data-target", "#staticBackdrop");
            } else if (creditRadio.checked) {
                creditRadio.setAttribute("data-target", "#staticBackdrop2");
                btnDeposit.onclick = openCreditModal;
                textLoadingBtn.innerText = "Processar";
            }
        }

        creditRadio.addEventListener("change", toggleButtonSubmit);
        pixRadio.addEventListener("change", toggleButtonSubmit);

        function showDepositValue() {
            let valueDeposit = depositInput.value;
            let formattedDeposit = valueDeposit.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
            inputPix.value = "R$" + formattedDeposit;
        }

        function loading(show) {
            if (show) {
                btnDeposit.disabled = true;
                spinner.style.display = 'flex';
            } else {
                btnDeposit.disabled = false;
                spinner.style.display = 'none';
            }
        }

        function copyCodePix() {
            navigator.clipboard.writeText(pixCopiaECola)
                .then(() => {
                    launchToast("success", trans("Success"), "Pix copiado para area de transferencia");
                })
                .catch(err => {
                    launchToast("danger", trans("Error"), "Erro ao copiar pix");
                });
        }

        const deposit = async () => {
            const hostname = window.location.origin;
            const url = `${hostname}/payment/deposit`;

            try {
                loading(true);

                if (Number(depositInput.value) < Number(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}`)) {
                    throw new Error(`O valor minimo para deposito e de ${`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}`}`)
                }

                let data = {
                    transaction_type: "deposit",
                    provider: pixRadio.checked ? "pix" : "card",
                    amount: depositInput.value
                }


                if (creditRadio.checked) {
                    $('#staticBackdrop2').modal('hide');
                    const cardToken = await generateCardToken()
                    data.cardToken = cardToken;
                }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const responseData = await response.json();

                if (!response.ok) {
                    throw new Error(responseData.message);
                }

                if (!responseData) {
                    throw new Error("Ocorreu um erro interno.");
                }

                if (data.provider === "card") {
                    return launchToast("success", trans("Success"), "Pagamento bem sucedido");
                }

                if (data.provider === "pix") {
                    document.getElementById("qrcode").innerHTML = "";
                    document.getElementById("amountPix").innerHTML = "";
                    pixCopiaECola = await responseData.pixCopiaECola;

                    var qrcode = new QRCode(document.getElementById("qrcode"), {
                        text: pixCopiaECola,
                        width: 200,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    })

                    const expiration_date = await responseData.calendario.expiracao

                    calculateTimeDifference(expiration_date);

                    amountPix.innerText = "R$ " + responseData.valor.original;

                    $('#staticBackdrop').modal('show');

                    launchToast("success", trans("Success"), "Pix gerado com sucesso");
                }

            } catch (error) {
                launchToast("danger", trans("Error"), error.message);
            } finally {
                loading(false);
            }
        }

        const openCreditModal = () => {
            $('#staticBackdrop2').modal('show');
        }

        const generateCardToken = async () => {
            try {
                if (typeof EfiPay === 'undefined') {
                    throw new Error('O script da Efipay não foi carregado.');
                    return;
                }

                const efiPay = EfiPay.CreditCard.setAccount("ac82fa088699e475f01e8703227a7da4")
                    .setEnvironment("production");

                const brand = await EfiPay.CreditCard
                    .setCardNumber(cardNumber.value.split(" ").join(""))
                    .verifyCardBrand();

                const cardData = {
                    brand: brand,
                    number: cardNumber.value.split(" ").join(""),
                    cvv: cardCVV.value,
                    expirationMonth: cardDateValidate.value.split("/")[0],
                    expirationYear: cardDateValidate.value.split("/")[1],
                    reuse: true,
                };

                const result = await efiPay.setCreditCardData(cardData).getPaymentToken();

                return result.payment_token;
            } catch (error) {
                let errorMessage = "Tente novamente mais tarde"

                switch (error.error) {
                    case 'erro_gn_fingerprint':
                        errorMessage = "Desative seu AdBlock"
                        break
                    case 'invalid_data':
                        errorMessage = error.error_description
                        break
                    default:
                        errorMessage = error.message
                        break
                }
                throw new Error(errorMessage)
            }
        }

        function calculateTimeDifference() {
            const now = new Date();
            const expiration = new Date(now.getTime() + 3600000);
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

                dataExpiration.innerText = `${formattedHours}h ${formattedMinutes}min ${formattedSeconds}s`;
            };

            intervalId = setInterval(updateRemainingTime, 1000);
            updateRemainingTime();
        }

        cardDateValidate.addEventListener('input', (event) => {
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

                    if (year.length === 4 && Number(year) < currentYear || year.length === 4 && Number(year) > currentYear + 10) {
                        launchToast("danger", trans("Error"), `Ano inválido. O ano deve estar entre ${currentYear} e ${currentYear + 10}.`);
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

        cardCVV.addEventListener('input', (event) => {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 3) {
                launchToast("danger", trans("Error"), "O código CVV deve conter 3 dígitos.");
                value = ''
            }
            event.target.value = value;
        })

        cardNumber.addEventListener('input', (event) => {
            let value = event.target.value.replace(/\D/g, '');
            value = value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
            event.target.value = value;
        });
    </script>