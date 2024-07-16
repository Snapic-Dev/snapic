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
                <button class="payment-button" type="submit"></button>
            </form>
        </div>

        <div class="paymentOption ml-2 paymentStripe d-none">
            <button id="stripe-checkout-button">{{__('Checkout')}}</button>
        </div>

        <!-- Modal -->
        <div class="checkout-popup modal fade" id="checkout-center" tabindex="-1" role="dialog"
            aria-labelledby="checkout" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-md p-2 font-weight-bold" id="payment-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="payment-body">
                            <div class="d-flex flex-row mt-1">
                                <div class="ml-0 ml-md-2 mb-2">
                                    <img src="" class="rounded-circle user-avatar">
                                </div>
                                <div class="d-lg-block">
                                    <div class="pl-2 d-flex justify-content-center flex-column">
                                        <div class="ml-2">
                                            <div
                                                class="text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}} name">
                                            </div>
                                            <a class="walletPerfil mt-1" href="/my/settings/wallet">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @include('elements.icon', ['icon' => 'wallet-outline', 'variant' => 'small'])
                                                </div>
                                                <span class=" font-weight-medium wallet-total-amount">
                                                    {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(number_format(Auth::user()->wallet->total, 2, '.', '')) }}
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3 checkout-amount-input d-none mt-3 p-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="amount-label">
                                        @include('elements.icon', ['icon' => 'cash-outline', 'variant' => 'medium', 'centered' => false])
                                    </span>
                                </div>
                                <input class="form-control uifield-amount"
                                    placeholder="{{__(\App\Providers\SettingsServiceProvider::leftAlignedCurrencyPosition() ? 'Amount ($5 min, $500 max)' : 'Amount (5$ min, 500$ max)', ['min' => getSetting('payments.min_tip_value'), 'max' => getSetting('payments.max_tip_value'), 'currency' => config('app.site.currency_symbol')])}}"
                                    aria-label="Username" aria-describedby="amount-label" id="checkout-amount"
                                    type="number" min="0" step="1" max="500">
                                <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
                            </div>
                        </div>
                        <!-- 
                        <div id="accordion" class="mb-3">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between" id="headingOne" data-toggle="collapse" data-target="#billingInformation" aria-expanded="true" aria-controls="billingInformation">
                                    <h6 class="mb-0">
                                        {{__('Billing agreement details')}}
                                    </h6>
                                    <div class="ml-1 label-icon">
                                        @include('elements.icon',['icon'=>'chevron-down-outline','centered'=>false])
                                    </div>
                                </div>
                                <div id="billingInformation" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                        <form id="billing-agreement-form">
                                            <div class="tab-content">
                                                <div id="individual" class="tab-pane fade show active pt-1">
                                                    <div class="row form-group">
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="firstName">
                                                                    <span>{{__('First name')}}</span>
                                                                </label>
                                                                <input type="text" name="firstName" placeholder="{{__('First name')}}" onchange="checkout.validateFirstNameField();" required class="form-control uifield-first_name">
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="lastName">
                                                                    <span>{{__('Last name')}}</span>
                                                                </label>
                                                                <input type="text" name="lastName" placeholder="{{__('Last name')}}" onblur="checkout.validateLastNameField()" required class="form-control uifield-last_name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="countrySelect">
                                                            <span>{{__('Country')}}</span>
                                                        </label>
                                                        <select class="country-select form-control input-sm uifield-country" id="countrySelect" required onchange="checkout.validateCountryField()"></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="billingCity">
                                                            <span>{{__('City')}}</span>
                                                        </label>
                                                        <input type="text" name="billingCity" placeholder="{{__('City')}}" onblur="checkout.validateCityField()" required class="form-control uifield-city">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="billingState">
                                                                    <span>{{__('State')}}</span>
                                                                </label>
                                                                <input type="text" name="billingState" placeholder="{{__('State')}}" onblur="checkout.validateStateField()" required class="form-control uifield-state">
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6 col-6">
                                                            <div class="form-group">
                                                                <label for="billingPostcode">
                                                                    <span>{{__('Postcode')}}</span>
                                                                </label>
                                                                <input type="text" name="billingPostcode" placeholder="{{__('Postcode')}}" onblur="checkout.validatePostcodeField()" required class="form-control uifield-postcode">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cardNumber">
                                                            <span>{{__('Address')}}</span>
                                                        </label>
                                                        <textarea rows="2" type="text" name="billingAddress" onblur="checkout.validateBillingAddressField()" placeholder="{{__('Street address, apartment, suite, unit')}}" class="form-control w-100 uifield-billing_address" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="billing-agreement-error error text-danger d-none">{{__('Please complete all billing details')}}</div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                         -->
                        <div class="mb-3">
                            <!--<h6>{{__('Payment summary')}}</h6>-->
                            <!--<div class="subtotal row">
                                <span class="col-sm left"><b>{{__('Subtotal')}}:</b></span>
                                <span class="subtotal-amount col-sm right text-right">
                                    <b>$0.00</b>
                                </span>
                            </div>-->
                            <div class="taxes-details"></div>
                            <!--<div class="total row mt-1">
                                <span class="col-sm left"><b>{{__('Total')}}:</b></span>
                                <span class="total-amount col-sm right text-right">
                                    <b>$0.00</b>
                                </span>
                            </div>-->
                        </div>
                        <!--
                        <div class="total row p-3">
                            <span class="col-sm left"><b>{{__("Wallet")}}</b></span>
                            <span class="total-amount col-sm right text-right">
                                <div class="available-credit">
                                    ({{\App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount('0')}})</div>
                            </span>
                            </div>
                        -->
                    </div>
                    <div class="modal-footer p-4">
                        <button type="button" class="btn btn-round border"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                        <button type="submit" class="btn btn-primary checkout-continue-btn btn-round">{{__('Confirm')}}
                            <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
                                <span class="sr-only">{{__('Loading...')}}</span>
                            </div>
                        </button>
                    </div>
                    <div class="payment-error error text-danger text-bold d-none mb-1">
                        {{__('Please select your payment method')}}
                    </div>
                    <p class="text-muted mt-1 text-sm ml-2 mr-2 pl-4 pr-4 pb-4 pt-1 text-left">
                        <strong>{{__('Nota:')}}</strong>
                        {{__('Após clicar no botão, o valor correspondente será deduzido da sua carteira e enviado diretamente para o influenciador.')}}
                        <span
                            class="font-weight-bold">{{__('Todo o processo de pagamento será realizado na mesma página.')}}</span>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
</div>