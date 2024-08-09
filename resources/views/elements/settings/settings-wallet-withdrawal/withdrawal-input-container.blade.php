<div class="input-group mb-3 mt-3 d-block">
    <form class="needs-validation" novalidate>
        <div class="form-row p-3">
            <div class="col mb-3">
                <label class="text-sm text-bold" for="validationTooltip01">Chave Pix</label>
                <input type="text" class="form-control" placeholder="bcdefghijklmnopqrstuvwxyz0123456789" id="validationTooltip01" required>
                <div class="mt-4">
                    <label class="text-sm text-bold" for=" validationTooltip01">Valor do Saque</label>
                    <input type="number" class="form-control withdrawalInput" id="validationTooltip01" placeholder="Valor mínimo de R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00" min="{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}" step="1" max="{{ \App\Providers\PaymentsServiceProvider::getDepositMaximumAmount() }}" required>
                </div>
                <div class="valid-tooltip">
                    Looks good!
                </div>
            </div>
        </div>
    </form>
    <div class="ml-2 d-flex justify-content-center">
        <button class="btn10 btn btn-round  border ml-2" onclick="inputWithdrawalValue(`{{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }}`)">
            R${{ \App\Providers\PaymentsServiceProvider::getDepositMinimumAmount() }},00
        </button>
        <button class="btn50 btn btn-round border ml-2" onclick="inputWithdrawalValue(50)">
            R$50,00
        </button>
        <button class="btn100 btn btn-round border ml-2" onclick="inputWithdrawalValue(100)">
            R$100,00
        </button>
        <button class="btn200 btn btn-round border ml-2" onclick="inputWithdrawalValue(200)">
            R$200,00
        </button>
        <button class="btn400 btn btn-round border ml-2" onclick="inputWithdrawalValue(400)">
            R$400,00
        </button>
        <button class="btn1000 btn btn-round border ml-2" onclick="inputWithdrawalValue(1000)">
            R$1000,00
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
        <button class="btn-block btn-round btn border btn-primary p-3 withdrawal-continue-btn" type="submit">{{__('Request withdrawal')}}</button>
    </div>
    <div class="p-3 pb-4 mt-4">
        <p class="text-sm alertWitdrawalMsg">
        <strong>Aviso Importante:</strong>
        Restrição de Idade para Saques
        Por favor, esteja ciente de que saques só podem ser realizados por indivíduos maiores de <strong>18 anos</strong>. Qualquer tentativa de saque por <strong>menores de idade</strong> será rejeitada conforme nossa <strong>política de segurança</strong>.
        </p>
    </div>
</div>

<script>
    let withdrawalInput = document.querySelector('.withdrawalInput')

    let btn10 = document.querySelector('.btn10');
    let btn50 = document.querySelector('.btn50');
    let btn100 = document.querySelector('.btn100');
    let btn200 = document.querySelector('.btn200');
    let btn400 = document.querySelector('.btn400');
    let btn1000 = document.querySelector('.btn1000');


    function inputWithdrawalValue(withdrawalValue) {
        withdrawalInput.value = withdrawalValue;
    }
</script>