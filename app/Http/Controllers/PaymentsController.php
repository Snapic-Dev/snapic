<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Http\Requests\CreateTransactionRequest;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\Withdrawal;
use App\Providers\InvoiceServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentRequestServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\Providers\WithdrawalsServiceProvider;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Stripe\StripeClient;
use Yabacon\Paystack;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    protected $paymentHandler;

    /**
     * PaymentsController constructor.
     * @param PaymentsServiceProvider $paymentsProvider
     */
    public function __construct(PaymentHelper $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }
    public function paymentInitiateValidator(CreateTransactionRequest $request)
    {
        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Initiates the payment based on the required provider.
     * @param CreateTransactionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initiatePayment(CreateTransactionRequest $request)
    {
        $transactionType = $request->get('transaction_type');
        $redirectLink = null;
        // generate one time transaction
        try {
            $transaction = new Transaction();
            $transaction['sender_user_id'] = Auth::user()->id;
            $transaction['recipient_user_id'] = $request->get('recipient_user_id');
            $transaction['post_id'] = $request->get('post_id');
            $transaction['user_message_id'] = $request->get('user_message_id');
            $transaction['type'] = $transactionType;
            $transaction['status'] = Transaction::INITIATED_STATUS;
            $transaction['amount'] = $request->get('amount');
            $transaction['currency'] = config('app.site.currency_code');
            $transaction['payment_provider'] = $request->get('provider');
            $transaction['taxes'] = $request->get('taxes');
            $transaction['stream_id'] = $request->get('stream');
            $errorMessage = __('Something went wrong with this transaction. Please try again');

            $recipientUser = User::query()->where('id', $transaction['recipient_user_id'])->first();
            if ($transaction['amount'] <= 0 || (!$recipientUser && $transactionType !== Transaction::DEPOSIT_TYPE)) {
                return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
            }

            if (!$this->paymentHandler->validateTransaction($transaction, $recipientUser)) {
                return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
            }

            if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                $this->paymentHandler->initiatePaypalContext();
            }

            if (in_array($transaction['payment_provider'], [Transaction::STRIPE_PROVIDER, Transaction::OXXO_PROVIDER])) {
                $redirectLink = $this->paymentHandler->generateStripeSessionByTransaction($transaction);
                // if we cannot fetch a redirect link it means stripe session generation process failed
                if ($redirectLink == null) {
                    $transaction['status'] = Transaction::DECLINED_STATUS;
                    $transaction->save();
                    return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage = __('Failed generating stripe session'));
                }
            }

            if ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                $userAvailableAmount = $this->paymentHandler->getLoggedUserAvailableAmount();
                // check if user have enough money to pay with credit for this transaction
                if ($userAvailableAmount < $transaction['amount']) {
                    $errorMessage = __("You don't have enough money to pay with credit for this transaction. Please try with another payment method");

                    return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
                }
            }

            switch ($transactionType) {
                case Transaction::TIP_TYPE:
                case Transaction::CHAT_TIP_TYPE:
                case Transaction::STREAM_ACCESS:
                case Transaction::POST_UNLOCK:
                case Transaction::MESSAGE_UNLOCK:
                    $userId = Auth::user()->id;
                    $postId = $transaction['post_id'];
                    $streamId = $transaction['stream_id'];
                    $messageId = $transaction['user_message_id'];
                    if ($recipientUser->id === $transaction['sender_user_id']) {
                        return $this->paymentHandler->redirectByTransaction(
                            $transaction,
                            $errorMessage = __('Cannot pay to yourself.')
                        );
                    }

                    if ($transactionType === Transaction::POST_UNLOCK && PostsHelperServiceProvider::userPaidForPost($userId, $postId)) {
                        return $this->paymentHandler->redirectByTransaction(
                            $transaction,
                            $errorMessage = __('You already unlocked this post.')
                        );
                    } elseif ($transactionType === Transaction::STREAM_ACCESS && PostsHelperServiceProvider::userPaidForStream($userId, $streamId)) {
                        return $this->paymentHandler->redirectByTransaction(
                            $transaction,
                            $errorMessage = __('You already paid for this streaming')
                        );
                    } elseif ($transactionType === Transaction::MESSAGE_UNLOCK && PostsHelperServiceProvider::userPaidForMessage($userId, $messageId)) {
                        return $this->paymentHandler->redirectByTransaction(
                            $transaction,
                            $errorMessage = __('You already paid access for this message')
                        );
                    }

                    $this->paymentHandler->generateOneTimeCreditTransaction($transaction);

                    break;
                case Transaction::DEPOSIT_TYPE:
                    $transaction['recipient_user_id'] = Auth::user()->id;
                    if ($transaction['pix'] == Transaction::PIX_PROVIDER) {

                        $res = $this->paymentHandler->generationPixPayment($transaction);
                        $transaction['status'] = 'pending';
                        $transaction->save();

                        return response()->json($res, 201);
                    } elseif ($transaction['card'] == Transaction::CARD_PROVIDER) {
                        // processar os dados do cartao
                    }
                    break;
                case Transaction::ONE_MONTH_SUBSCRIPTION:
                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                case Transaction::YEARLY_SUBSCRIPTION:
                    if ($recipientUser->id === $transaction['sender_user_id']) {
                        return $this->paymentHandler->redirectByTransaction(
                            $transaction,
                            $errorMessage = __('Cannot subscribe to yourself.')
                        );
                    }

                    if (PostsHelperServiceProvider::hasActiveSub($transaction['sender_user_id'], $transaction['recipient_user_id'])) {
                        $errorMessage = __('You already have an active subscription for this user.');

                        return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
                    }

                    $this->paymentHandler->generateCreditSubscriptionByTransaction($transaction);
                    break;
                default:
                    return $this->paymentHandler->redirectByTransaction($transaction);
            }
            $transaction->save();

            if (
                $transaction['payment_provider'] === Transaction::CREDIT_PROVIDER
                && $transaction['status'] === Transaction::APPROVED_STATUS
            ) {
                $this->paymentHandler->creditReceiverForTransaction($transaction);
                $this->paymentHandler->deductMoneyFromUserWalletForCreditTransaction($transaction, Auth::user()->wallet);
                $this->paymentHandler->createNewTipNotificationForCreditTransaction($transaction);
                NotificationServiceProvider::createPPVNotificationByTransaction($transaction);
            }

            // create payment request for this transaction and leave it on initiated status
            if ($transaction['payment_provider'] === Transaction::MANUAL_PROVIDER) {
                $manualPaymentFiles = $request->get('manual_payment_files');
                $manualPaymentDescription = $request->get('manual_payment_description');
                PaymentRequestServiceProvider::createDepositPaymentRequestByTransaction($transaction, $manualPaymentFiles, $manualPaymentDescription);
            }

            if ($transaction != null) {
                try {
                    $invoice = InvoiceServiceProvider::createInvoiceByTransaction($transaction);
                    if ($invoice != null) {
                        $transaction->invoice_id = $invoice->id;
                        $transaction->save();
                    }
                } catch (\Exception $exception) {
                    Log::channel('payments')->error("Failed generating invoice for transaction: " . $transaction->id . " error: " . $exception->getMessage());
                }
            }
        } catch (\Exception $exception) {
            Log::channel('payments')->error("Payment failed -> error message: " . $exception->getMessage());
            Log::channel('payments')->error("Payment failed", [$exception->getTraceAsString()]);

            return Redirect::route('feed')
                ->with('error', __('Payment failed.'));
        }

        // Url generated successfully
        if (isset($redirectLink) && in_array($transaction['payment_provider'], Transaction::ALLOWED_PAYMENT_PROVIDERS)) {
            // redirect on payment provider checkout page
            return Redirect::away($redirectLink);
        }
        return $this->paymentHandler->redirectByTransaction($transaction);
    }

    /**
     * Handles NowPayments payment execution
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAndUpdateNowPaymentsTransaction(Request $request)
    {
        $nowPaymentsTransactionToken = $request->get('orderId');
        $transaction = null;
        if ($nowPaymentsTransactionToken) {
            $transaction = Transaction::query()->where('nowpayments_order_id', $nowPaymentsTransactionToken)->first();
            if ($transaction) {
                $this->paymentHandler->checkAndUpdateNowPaymentsTransaction($transaction);
                $transaction->save();
            }
        }

        return $this->paymentHandler->redirectByTransaction($transaction);
    }

    /**
     * Process NowPayments IPN hooks
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nowPaymentsHook(Request $request)
    {
        if (!getSetting('payments.nowpayments_ipn_secret_key')) {
            Log::channel('payments')->info("NowPayments hook error: missing IPN secret key");
            return response()->json([
                'status' => 400
            ], 400);
        }

        try {
            if (isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) && !empty($_SERVER['HTTP_X_NOWPAYMENTS_SIG'])) {
                $received_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];
                $request_json = $request->getContent();
                $payload = json_decode($request_json, true);
                Log::channel('payments')->info("NowPayments hook received: ", [$payload]);
                ksort($payload);
                $sorted_request_json = json_encode($payload, JSON_UNESCAPED_SLASHES);
                if ($request_json !== false && !empty($request_json)) {
                    $hmac = hash_hmac("sha512", $sorted_request_json, trim(getSetting('payments.nowpayments_ipn_secret_key')));
                    if ($hmac == $received_hmac) {
                        Log::channel('payments')->info("NowPayments hook payload: ", [$payload]);
                        if (isset($payload['order_id']) && isset($payload['payment_status']) && isset($payload['payment_id'])) {
                            $transaction = Transaction::query()->where('nowpayments_order_id', $payload['order_id'])->with('receiver')->first();
                            if ($transaction) {
                                if (in_array($transaction->status, [Transaction::INITIATED_STATUS, Transaction::PENDING_STATUS, Transaction::PARTIALLY_PAID_STATUS])) {
                                    // payment approved
                                    if ($payload['payment_status'] === 'finished') {
                                        $transaction->status = Transaction::APPROVED_STATUS;
                                        $this->paymentHandler->creditReceiverForTransaction($transaction);
                                        NotificationServiceProvider::createTipNotificationByTransaction($transaction);
                                        NotificationServiceProvider::sendApprovedDepositTransactionEmailNotification($transaction);
                                        NotificationServiceProvider::createPPVNotificationByTransaction($transaction);
                                        // payment pending
                                    } elseif ($transaction->status !== Transaction::PENDING_STATUS && in_array($payload['payment_status'], ['waiting', 'confirming', 'sending'])) {
                                        $transaction->nowpayments_payment_id = $payload['payment_id'];
                                        $transaction->status = Transaction::PENDING_STATUS;
                                        // payment partially paid
                                    } elseif ($payload['payment_status'] === 'partially_paid' && $transaction->status !== Transaction::PARTIALLY_PAID_STATUS) {
                                        $transaction->status = Transaction::PARTIALLY_PAID_STATUS;
                                        NotificationServiceProvider::sendNowPaymentsPartiallyPaidTransactionEmailNotification($transaction);
                                        // payment expired or failed
                                    } elseif (in_array($payload['payment_status'], ['expired', 'failed'])) {
                                        $transaction->status = Transaction::DECLINED_STATUS;
                                    }
                                    $transaction->save();
                                    // handle refund
                                } else if ($transaction->status === Transaction::APPROVED_STATUS && $payload['payment_status'] === 'refunded') {
                                    $this->paymentHandler->deductMoneyFromUserForRefundedTransaction($transaction);
                                    $transaction->status = Transaction::REFUNDED_STATUS;
                                    $transaction->save();
                                }
                            }
                        }

                        return response()->json([
                            'status' => 200
                        ], 200);
                    } else {
                        Log::channel('payments')->info('NowPayments HMAC signature does not match');
                    }
                } else {
                    Log::channel('payments')->info('NowPayments Error reading POST data');
                }
            } else {
                Log::channel('payments')->info('NowPayments No HMAC signature sent.');
            }
        } catch (\Exception $exception) {
            Log::channel('payments')->info("NowPayments hook error: ", [$exception->getMessage()]);
        }

        return response()->json([
            'status' => 400
        ], 400);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
}
