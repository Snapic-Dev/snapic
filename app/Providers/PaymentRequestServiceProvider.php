<?php

namespace App\Providers;

use App\Model\Attachment;
use App\Model\PaymentRequest;
use App\Model\Stream;
use App\Model\Wallet;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class PaymentRequestServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //        UserVerify::observe(UserVerifyObserver::class);
        Schema::defaultStringLength(191);
    }

    /**
     * Creates a payment request for admins by a transaction
     * @param $transaction
     */
    public static function createDepositPaymentRequestByTransaction($transaction, $files, $description)
    {
        $paymentRequest = PaymentRequest::create([
            'type' => PaymentRequest::DEPOSIT_TYPE,
            'user_id' => $transaction['recipient_user_id'],
            'transaction_id' => $transaction['id'],
            'amount' => $transaction['amount'],
            'message' => $description
        ]);

        if ($paymentRequest) {
            if ($files && strlen($files) > 0) {
                $filesArray = explode(',', $files);
                if (count($filesArray)) {
                    foreach ($filesArray as $attachmentId) {
                        $attachment = Attachment::query()->where('id', $attachmentId)->first();
                        if ($attachment != null) {
                            $attachment->update(['payment_request_id' => $paymentRequest['id']]);
                        }
                    }
                }
            }

            // Sending out admin email
            $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
            foreach ($adminEmails as $user) {
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $user->email,
                        'subject' => __('Ação necessária | Nova solicitação de pagamento'),
                        'title' => __('Olá, :name,', ['name' => $user->name]),
                        'content' => __('Há uma nova solicitação de pagamento em :siteName que requer sua atenção.', ['siteName' => getSetting('site.name')]),
                        'button' => [
                            'text' => __('Ir para o admin'),
                            'url' => route('voyager.dashboard') . '/payment-requests',
                        ],
                    ]
                );
            }
        }
    }
}
