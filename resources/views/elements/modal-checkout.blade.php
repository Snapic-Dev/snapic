<div class="modal fade" tabindex="-1" role="dialog" id="checkoutModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content p-2 modalcheckout">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="block-user-label">Acesse o Conteúdo Exclusivo</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modalcheckout">
            <div class="row">
                <!-- Coluna para telas grandes (col-lg-6) e tela pequena (col-12) -->
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-column align-items-center justify-content-center card-wrapper">
                        @include('elements.vertical-member-card', ['profile' => $user])
                    </div>
                </div>
                
                <!-- Coluna para telas grandes (col-lg-6) e tela pequena (col-12) -->
                <div class="col-12 col-lg-6 d-flex align-items-start pl-0">
                    <div class="d-flex flex-column align-items-center boxTest">
                        <div class="d-flex justify-content-center p-4">
                            <h1 class="titleCheckoutModal text-bold">
                                Assinatura mensal de  {{ $user->name }}
                            </h1>
                        </div>
                        <div class="amountText d-flex justify-content-between p-2">
                            <h4>Pagamento Total</h4>
                            <h5 id="amountPix">R$50,00</h5>
                        </div>
                        <div class="line"></div>
                        <div class="timePayment d-flex justify-content-between p-2">
                            <h4>Pagar em até</h4>
                            <h5 id="dataExpiration" aria-placeholder="00h 00min 00s">00:00</h5>
                        </div>
                        <div class="p-4 mt-3 mb-2">
                            <div id="qrcode-checkout"></div>
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
                                    @include('elements.icon', ['icon' => 'cash-outline', 'variant' => 'small'])
                                </div>
                                Copiar código PIX
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>