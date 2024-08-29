<div class="input-group mb-3 mt-3 d-block">
    <form class="needs-validation" novalidate>
        <div class="form-row p-3">
            <div class="col mb-3">
                <label class="text-sm text-bold" for="validationTooltip01">Chave Pix</label>
                <input type="text" class="form-control" placeholder="6d6e36e5-2c43-4c8e-823d-9e9a2d2f5b67" id="validationTooltip01" required>
                <div class="mt-4">
                    <label class="text-sm text-bold" for=" validationTooltip01">Valor do Saque</label>
                    <input type="number" class="form-control withdrawalInput validationTooltip01" id="withdrawal-amount" placeholder="Valor mínimo de R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00" min="{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}" step="1" max="{{ \App\Providers\PaymentsServiceProvider::getDepositMaximumAmount() }}" required>
                </div>
                <div class="valid-tooltip">
                    Looks good!
                </div>
            </div>
        </div>
    </form>
    <div class="ml-2 d-flex justify-content-center mt-2">
        <button class="btnOne btn btn-round  border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00
        </button>
        <button class="btnTwo btn btn-round border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 100 }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 100 }},00
        </button>
        <button class="btnThree btn btn-round border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 300 }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 300 }},00
        </button>
        <button class="btnFour btn btn-round border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 500 }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 500 }},00
        </button>
        <button class="btnFive btn btn-round border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 800 }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 800 }},00
        </button>
        <button class="btnSix btn btn-round border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 1000 }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() + 1000 }},00
        </button>
    </div>
    <!-- <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
    </div> -->
    <!-- <input class="form-control" placeholder="{{ \App\Providers\PaymentsServiceProvider::getWithdrawalAmountLimitations() }}" aria-label="Username" aria-describedby="amount-label" id="withdrawal-amount" type="number" min="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMinimumAmount()}}" step="1" max="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMaximumAmount()}}"> -->
    <div class="invalid-feedback">{{__('Please enter a valid amount')}}</div>
    <!-- <div class="input-group mb-3 mt-3">
        <div class="d-flex flex-row w-100">
            <div class="form-group w-50 pr-2 p-2">
                <label for="paymentMethod">{{__('Payment method')}}</label>
                <select class="form-control" id="payment-methods" name="payment-methods">
                    @foreach(\App\Providers\PaymentsServiceProvider::getWithdrawalsAllowedPaymentMethods() as $paymentMethod)
                    <option value="{{$paymentMethod}}">{{__($paymentMethod)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group w-50 pl-2 update-stripe-connect-box d-none">
                <label id="update-stripe-connect-label" for="update-stripe-connect">{{__('Update details')}}</label>
                <a href="{{route('withdrawals.onboarding')}}">
                    <button id="update-stripe-connect" class="btn btn-primary btn-block rounded mr-0">{{__('Update')}}</button>
                </a>
            </div>
            <div class="form-group w-50 pl-2 input-label p-2">
                <label id="payment-identifier-label" for="withdrawal-payment-identifier">{{__("Bank account")}}</label>
                <input class="form-control" type="text" id="withdrawal-payment-identifier" name="payment-identifier">
            </div>
        </div>
        <div class="form-group w-100 input-message p-2">
            <label for="withdrawal-message">{{__('Message (Optional)')}}</label>
            <textarea placeholder="{{__('Bank account, notes, etc')}}" class="form-control" id="withdrawal-message" rows="2"></textarea>
            <span class="invalid-feedback" role="alert">
                {{__('Please add your withdrawal notes: EG: Paypal or Bank account.')}}
            </span>
        </div>
    </div> -->
    <!-- <div class="stripe-connect-label d-none">
        @if(!Auth::user()->country_id)
        <span>{{__("You must set the country on your profile before you can start onboarding and withdraw money")}}</span>
        @elseif(!Auth::user()->stripe_onboarding_verified)
        <span>{{__("We're using Stripe to get you paid quickly and keep your personal and payment information secure. Thousands of companies around the world trust Stripe to process payments for their users. Set up a Stripe account to get paid with us")}}</span>
        @endif
    </div>
    <div class="payment-error error text-danger d-none mt-3">{{__('Add all required info')}}</div>
    <div class="stripe-connect-buttons d-none w-100">
        @if(!Auth::user()->country_id)
        <div class="mt-3">
            <div>
                <a href="{{route('my.settings',['type'=>'profile'])}}">
                    <button class="btn btn-primary btn-block rounded mr-0">{{__('Set your country')}}</button>
                </a>
            </div>
        </div>
        @elseif(!Auth::user()->stripe_onboarding_verified)
        <div class="mt-3">
            <div>
                <a href="{{route('withdrawals.onboarding')}}">
                    <button class="btn btn-primary btn-block rounded mr-0">{{!Auth::user()->stripe_account_id ? __('Start onboarding') : __('Update details')}}</button>
                </a>
            </div>
        </div>
        @endif
    </div> -->

    <div class="mt-4">
        <button class="btn-block btn-round btn border btn-primary p-3 withdrawal-continue-btn" type="submit" disabled="true">
            {{__('Request withdrawal')}}
        </button>
    </div>
    <div class="p-3 pb-4 mt-4">
        <p class="text-xs alertWitdrawalMsg">
            <strong>Aviso Importante:</strong>
            Restrição de Idade para Saques
            Por favor, esteja ciente de que saques só podem ser realizados por indivíduos maiores de <strong>18 anos</strong>. Qualquer tentativa de saque por <strong>menores de idade</strong> será rejeitada conforme nossa <strong>política de segurança</strong>.
        </p>
    </div>
</div>

<script src="{{ asset('C:\Users\Pichau\Documents\Programação\Php\snapic\public\js\pages\settings\withdrawal.js') }}"></script>
<script>
    let withdrawalInput = document.querySelector('.withdrawalInput')
    let withdrawalContinueBtn = document.querySelector('.withdrawal-continue-btn')

    let btnOne = document.querySelector('.btnOne');
    let btnTwo = document.querySelector('.btnTwo');
    let btnThree = document.querySelector('.btnThree');
    let btnFour = document.querySelector('.btnFour');
    let btnFive = document.querySelector('.btnFive');
    let btnSix = document.querySelector('.btnSix');


    function inputWithdrawalValue(withdrawalValue) {
        withdrawalInput.value = withdrawalValue;
        validateInput();
    }

    function validateInput() {
        const minWithdrawalAmount = `{{\App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(number_format(Auth::user()->wallet->total, 2, '.', ''))}}`;
        let InputWithdrawalValue = withdrawalInput.value;
        let latestWithdrawal = `{{\App\Providers\PaymentsServiceProvider::verifyDiaryWithdrawal(Auth::user()->id)}}`

        if (InputWithdrawalValue !== "" && latestWithdrawal) {
            console.log(InputWithdrawalValue < 100 && minWithdrawalAmount < InputWithdrawalValue)
            if (InputWithdrawalValue < 100) {
                withdrawalContinueBtn.disabled = true;
            } else {
                withdrawalContinueBtn.disabled = false;
            }
        } else {
            withdrawalContinueBtn.disabled = true;
        }
    }

    withdrawalInput.addEventListener('input', validateInput)
</script>