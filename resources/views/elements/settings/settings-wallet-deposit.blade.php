<h5 class="mt-4 p-2 text-bold text-md">{{ __('Proceed with payment') }}</h5>
<div class="input-group mb-3 mt-3 p-2">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon', ['icon' => 'cash-outline', 'variant' => 'medium'])</span>
    </div>
    <input class="form-control inputText depositInput" placeholder="{{ \App\Providers\PaymentsServiceProvider::getDepositLimitAmounts() }}" aria-label="{{ __('Username') }}" aria-describedby="amount-label" id="deposit-amount" type="number" min="{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}" step="1" max="{{ \App\Providers\PaymentsServiceProvider::getDepositMaximumAmount() }}">
    <div class="invalid-feedback">{{ __('Please enter a valid amount.') }}</div>
</div>
<div class="feedbackForUser text-sm mb-3 ml-2 text-bold"></div>

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

        <!-- Button trigger modal -->
        <div class="mt-4">
            <!-- Modal -->
            <div class="modal fade show" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <button type="button" onclick="generatePix()" class="modalCreditCard btn-block btn-round btn border btn-primary p-3" data-target="#staticBackdrop" onclick="showDepositValue()" <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
                <div class="d-flex justify-content-center">
                    <span class="spinner spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    <span class="textLoadingBtn">Depositar</span>
                </div>
            </button>
            <div class="modal fade" id="staticBackdrop2" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000">
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

    </div>
    @include('elements.uploaded-file-preview-template')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
        let dataExpiration=document.getElementById('dataExpiration');

        function showCreditInput() {
            if (pixRadio.checked) {
                modalCreditCard.setAttribute("data-target", "#staticBackdrop");
                pixRadioTxt.style.fontWeight = "bold";
                creditRadioTxt.style.fontWeight = "normal";
            } else {
                modalCreditCard.setAttribute("data-target", "#staticBackdrop2");
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
                            launchToast("danger", trans("Error"), `Network response was not ok ${response.statusText}`);
                            return;
                        }

                        const responseData = await response.json();
                        console.log(responseData);
                        const qrCode = await responseData.pixCopiaECola;
                        console.log(qrCode);


                        const expiration_date = await responseData.calendario.expiracao
                        console.log(expiration_date);


                        calculateTimeDifference(expiration_date);


                        if (responseData) {
                            document.getElementById("qrcode").innerHTML = "";
                            var qrcode = new QRCode(document.getElementById("qrcode"), {
                            text: responseData.pixCopiaECola,
                            width: 200,
                            height: 200,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                            })
                            amountPix.innerText = "R$" + depositInput.value;
                            $('#staticBackdrop').modal('show')
                            launchToast("success",
                            trans("Success"), "Pix gerado com sucesso");
                        }

                        pixCode = responseData.pixCopiaECola;
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

    </script>