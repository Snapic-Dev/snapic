<?php

namespace App\Http\Controllers;

use App\Events\NewUserMessage;
use App\Events\PaymentProcessed;
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
use Exception;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

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

    function convertToCents($amount)
    {
        $amount = str_replace(',', '.', $amount);
        $cents = (int) round($amount * 100);

        return $cents;
    }

    function handleErrorsCard($res)
    {
        try {
            if (isset($res['data']['status']) && $res['data']['status'] === Transaction::APPROVED_STATUS) {
                return 'approved';
            }

            if (isset($res['data']['status']) && $res['data']['status'] === 'unpaid') {
                throw new \Exception("Houve um erro ao processar seu pagamento. Por favor, verifique as informações e tente novamente.");
            }

            if (isset($res['error_description']['property'])) {
                switch ($res['error_description']['property']) {
                    case '/payment/credit_card/customer/name':
                        throw new \Exception("Nome do cliente está incorreto.");
                    case '/payment/credit_card/customer/birth':
                        throw new \Exception("Data de nascimento incorreta.");
                    case '/payment/credit_card/customer/phone_number':
                        throw new \Exception("Número de telefone está incorreto.");
                    case 'payment_token':
                        throw new \Exception("Erro ao gerar o token do cartão.");
                    case 'Limite de emissões diárias excedido. Por favor, solicite que o recebedor entre em contato com o suporte Gerencianet.':
                        throw new \Exception("Limite de emissões diárias excedido. Entre em contato com o suporte.");
                    default:
                        throw new \Exception("Erro desconhecido na propriedade: " . $res['error_description']['property']);
                }
            }

            if (isset($res['error_description'])) {
                switch ($res['error_description']) {
                    case 'Limite de emissões idênticas excedido. Por favor, entre em contato com nosso suporte para orientações sobre o uso correto dos serviços Gerencianet.':
                        throw new \Exception("Limite de emissões idênticas excedido. Contate o suporte.");
                    case 'Limite de emissões diárias excedido. Por favor, solicite que o recebedor entre em contato com o suporte Gerencianet.':
                        throw new \Exception("Limite de emissões diárias excedido. Contate o suporte.");
                    case 'CPF inválido.':
                        throw new \Exception("CPF informado é inválido.");
                    case '/payment/credit_card/customer/phone_number':
                        throw new \Exception("Número de telefone está incorreto.");
                    case 'Número do cartão é inválido.':
                        throw new \Exception("O número do cartão é inválido.");
                    default:
                        throw new \Exception("Erro desconhecido: " . $res['error_description']);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro no processamento do pagamento',
                'message' => $e->getMessage(),
            ], 401);
        }
    }


    protected function processPixPayment($transaction)
    {
        $res = $this->paymentHandler->generationPixPayment($transaction);

        if (isset($res['status']) && $res['status'] === 'ATIVA') {
            $transaction['status'] = 'pending';
            $transaction['transfer_id'] = $res['txid'];
            $transaction->save();

            return response()->json($res, 201);
        } else {
            $errorCode = $res['mensagem'] ?? null;

            switch ($errorCode) {
                case 'Documento CPF em devedor.cpf é inválido':
                    return response()->json([
                        'error' => 'CPF inválido',
                        'message' => 'O CPF informado é inválido. Por favor, verifique e tente novamente.',
                    ], 400);

                default:
                    return response()->json([
                        'error' => $errorCode ?: 'Erro desconhecido',
                        'message' => $errorCode ?: 'Ocorreu um erro desconhecido ao tentar gerar o pagamento via Pix.',
                    ], 400);
            }
        }
    }


    protected function processCardPayment($transaction, $amount, $cardToken, $title, $cpf, $name)
    {
        $value = $this->convertToCents($amount);


        $res = $this->paymentHandler->generationCardPayment($value, $cardToken, $title, $cpf, $name);
        $status = $this->handleErrorsCard($res);

        if ($status === 'approved') {
            $transaction['status'] = Transaction::APPROVED_STATUS;
            $transaction['transfer_id'] = $res['data']['charge_id'];
            $transaction['amount'] = $amount;
            $transaction->save();

            // Incrementa o saldo na carteira do usuário
            DB::table('wallets')
                ->where('user_id', $transaction['recipient_user_id'])
                ->increment('total', $amount);
            self::handleTransactionNotification($transaction);
            return response()->json($res, 201);
        } else {
            return response()->json([
                'error' => 'Erro ao processar o pagamento com cartão.',
                'message' => 'O pagamento não foi aprovado.'
            ], 401);
        }
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
                    $transactionTitle = '';

                    switch ($transactionType) {
                        case Transaction::TIP_TYPE:
                            $transactionTitle = "Gorjeta no Post de {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::CHAT_TIP_TYPE:
                            $transactionTitle = "Gorjeta no Chat de {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::STREAM_ACCESS:
                            $transactionTitle = "Acesso à Transmissão de {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::POST_UNLOCK:
                            $transactionTitle = "Desbloqueio do Post de {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::MESSAGE_UNLOCK:
                            $transactionTitle = "Desbloqueio de Mensagem de {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        default:
                            $transactionTitle = "Transação não identificada - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                    }

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

                    if ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                        $this->paymentHandler->generateOneTimeCreditTransaction($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::PIX_PROVIDER) {
                        if (!Auth::user()->cpf) {
                            return response()->json([
                                'error' => 'CPF não cadastrado.',
                                'message' => 'Cadastre seu CPF para prosseguir!',
                            ], 401);
                        }

                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::CARD_PROVIDER) {
                        return $this->processCardPayment($transaction, $request->amount, $request->card_token, $transactionTitle, $request->cpf, $request->name);
                    }

                    break;
                case Transaction::DEPOSIT_TYPE:
                    $transaction['recipient_user_id'] = Auth::user()->id;

                    if (!Auth::user()->cpf) {
                        return response()->json([
                            'error' => 'CPF não cadastrado.',
                            'message' => 'Adicione seu CPF para prosseguir!',
                        ], 401);
                    }

                    if ($transaction['payment_provider'] == Transaction::PIX_PROVIDER) {
                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::CARD_PROVIDER) {
                        $transactionTitle = "Depósito de R$" . number_format($transaction['amount'], 2, ',', '.') . " para a conta " . Auth::user()->name;
                        return $this->processCardPayment($transaction, $request->amount, $request->cardToken, $transactionTitle, Auth::user()->cpf, Auth::user()->name);
                    }

                    break;
                case Transaction::ONE_MONTH_SUBSCRIPTION:
                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                case Transaction::YEARLY_SUBSCRIPTION:
                    $transactionTitle = '';
                    switch ($transactionType) {
                        case Transaction::ONE_MONTH_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 1 Mês para {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::THREE_MONTHS_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 3 Meses para {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::SIX_MONTHS_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 6 Meses para {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::YEARLY_SUBSCRIPTION:
                            $transactionTitle = "Assinatura Anual para {$recipientUser->name} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        default:
                            $transactionTitle = "Transação não identificada - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                    }

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

                    if ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                        $this->paymentHandler->generateCreditSubscriptionByTransaction($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::PIX_PROVIDER) {
                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::CARD_PROVIDER) {
                        return $this->processCardPayment($transaction, $request->amount, $request->card_token, $transactionTitle, $request->cpf, $request->name);
                    }
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

            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => $exception->getMessage()
            ], 500);
        }

        // Url generated successfully
        if (isset($redirectLink) && in_array($transaction['payment_provider'], Transaction::ALLOWED_PAYMENT_PROVIDERS)) {
            // redirect on payment provider checkout page
            return Redirect::away($redirectLink);
        }
        return $this->paymentHandler->redirectByTransaction($transaction);
    }


    public function configWebhook(Request $request)
    {
        try {
        } catch (\Exception $e) {
            Log::error('Erro ao configurar o webhook:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Falha ao configurar o webhook',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function webhook(Request $request)
    {
        try {
            $pix = $request->json('pix')[0] ?? null;
            $txid = $pix['txid'] ?? null;
            $amount = isset($pix['valor']) ? (float) $pix['valor'] : null;
            $e2eid = $pix['endToEndId'] ?? null;

            $clientId = $request->header('clientid');
            $clientSecret = $request->header('clientsecret');

            $efipayClientId = env('EFIPAY_CLIENT_ID');
            $efipayClientSecret = env('EFIPAY_CLIENT_SECRET');

            $isUnauthorized =
                $clientId !== $efipayClientId ||
                $clientSecret !== $efipayClientSecret ||
                !$txid || !$amount || !$e2eid;

            if ($isUnauthorized) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if (!$txid) {
                return response()->json(['message' => 'txid não encontrado no corpo da requisição'], 400);
            }

            if (!preg_match('/^[\w-]+$/', $txid)) {
                return response()->json(['message' => 'txid inválido'], 400);
            }

            DB::table('transactions')
                ->where('transfer_id', $txid)
                ->update([
                    'status' => 'approved',
                    'e2eId' => $e2eid
                ]);

            $transaction = DB::table('transactions')
                ->where('transfer_id', $txid)
                ->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transação não encontrada'], 404);
            }

            DB::table('wallets')
                ->where('user_id', $transaction->recipient_user_id)
                ->increment('total', $amount);

            self::handleTransactionNotification($transaction);
            return response()->json(['message' => 'Pagamento Processado'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao processar o webhook PIX:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    function handleTransactionNotification($transaction)
    {
        if ($transaction->status === Transaction::APPROVED_STATUS) {
            switch ($transaction->type) {
                case Transaction::TIP_TYPE:
                    NotificationServiceProvider::createTipNotificationByTransaction($transaction);
                    break;

                case Transaction::CHAT_TIP_TYPE:
                    NotificationServiceProvider::createTipNotificationByTransaction($transaction);
                    break;

                case Transaction::STREAM_ACCESS:
                    NotificationServiceProvider::createPPVNotificationByTransaction($transaction);
                    break;

                case Transaction::POST_UNLOCK:
                    NotificationServiceProvider::createPPVNotificationByTransaction($transaction);
                    break;

                case Transaction::MESSAGE_UNLOCK:
                    NotificationServiceProvider::createPPVNotificationByTransaction($transaction);
                    break;

                case Transaction::ONE_MONTH_SUBSCRIPTION:
                    NotificationServiceProvider::createNewSubscriptionNotification($transaction);
                    break;

                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                    NotificationServiceProvider::createNewSubscriptionNotification($transaction);
                    break;

                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                    NotificationServiceProvider::createNewSubscriptionNotification($transaction);
                    break;

                case Transaction::YEARLY_SUBSCRIPTION:
                    NotificationServiceProvider::createNewSubscriptionNotification($transaction);
                    break;

                default:
                    break;
            }
        }
    }
}
