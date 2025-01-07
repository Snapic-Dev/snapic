<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Http\Requests\CreateTransactionRequest;
use App\Model\Agreement;
use App\Model\ReferralCodeUsage;
use App\Model\Reward;
use App\Model\Transaction;
use App\Model\Wallet;
use App\Providers\InvoiceServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\PixelServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\UsersServiceProvider;
use GuzzleHttp\Client;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    protected $paymentHandler;
    protected $pixelService;
    protected $MERCADOPAGO_BASE_URL;
    protected $ACCESS_TOKEN;

    /**
     * PaymentsController constructor.
     * @param PaymentsServiceProvider $paymentsProvider
     */
    public function __construct(PixelServiceProvider $pixelService, PaymentHelper $paymentHandler)
    {
        $this->pixelService = $pixelService;
        $this->paymentHandler = $paymentHandler;
        $this->MERCADOPAGO_BASE_URL = 'https://api.mercadopago.com/v1/';
        $this->ACCESS_TOKEN = 'APP_USR-6211710841718220-100720-d609bab0ef91a88404a9785a065a804b-2025141516 ';
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


    protected function processPixPayment($transaction)
    {
        $res = $this->paymentHandler->generationPixPayment($transaction);

        if (isset($res['status']) && $res['status'] === 'ATIVA') {
            $transaction['status'] = 'pending';
            $transaction['transfer_id'] = $res['txid'];
            $transaction->save();
            if (
                $transaction['ad'] ||
                $transaction['visitor_id']
            ) {
                $event_result = $this->pixelService->registerPurchase($transaction->amount, "Iniciate checkout");
            }
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

    function getCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        } elseif (preg_match('/^(50|51|52|53|54|55)/', $cardNumber)) {
            return 'master';
        } elseif (preg_match('/^(34|37)/', $cardNumber)) {
            return 'amex';
        } else {
            throw new Exception("O cartão informado não é aceito. Por favor, utilize um cartão Visa, MasterCard ou American Express.");
        }
    }

    function createToken($token)
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);

        try {
            $response = $client->post($this->MERCADOPAGO_BASE_URL . 'card_tokens', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->ACCESS_TOKEN,
                ],
                'json' => $token,
            ]);

            $body = json_decode($response->getBody(), true);
            if (empty($body['id'])) {
                throw new Exception('Erro em processar pagamento: ID do token não encontrado.');
            }

            return $body['id'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if (method_exists($e, 'getResponse')) {
                $response = $e->getResponse();
                if ($response) {
                    $responseBody = json_decode($response->getBody()->getContents(), true);

                    if (isset($responseBody['message'])) {
                        switch ($responseBody['message']) {
                            case "invalid card_number":
                                throw new Exception('Número do cartão inválido');
                            default:
                                throw new Exception('Erro: ' . $responseBody['message']);
                        }
                    }
                }
            }
            throw new Exception('Erro ao criar token: ' . $e->getMessage());
        }
    }

    public function generationCardPayment($token, $title, $user, $amount, $brand, $transaction)
    {
        try {
            $idempotencyKey = uniqid();
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->post($this->MERCADOPAGO_BASE_URL . 'payments', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->ACCESS_TOKEN,
                    'X-Idempotency-Key' => $idempotencyKey,
                ],
                'json' => $this->preparePaymentData($token, $title, $user, $amount, $brand),
            ]);

            $body = json_decode($response->getBody(), true);

            if ($body['status'] === 'rejected' && $body['status_detail'] === 'cc_rejected_high_risk') {
                if (
                    $transaction['ad'] ||
                    $transaction['visitor_id']
                ) {
                    $event_result = $this->pixelService->registerPurchase($transaction->amount, "Iniciate checkout");
                }
                throw new Exception('Seu pagamento foi rejeitado devido a alto risco. Tente outro método de pagamento ou contate seu banco.');
            }

            if ($body['status'] !== 'approved') {
                $event_result = $this->pixelService->registerPurchase($amount, "Iniciate checkout");
                throw new Exception('Pagamento recusado');
            }
            return $body;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'error' => 'Payment not approved',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function preparePaymentData($token, $title, $user, $amount, $brand)
    {
        return [
            'transaction_amount' => round($amount),
            'token' => $token,
            'description' => $title,
            'installments' => 1,
            'payment_method_id' => $brand,
            'payer' => [
                'entity_type' => 'individual',
                'type' => 'customer',
                'email' => $user->email,
                'identification' => [
                    'type' => 'CPF',
                    'number' => $user->cpf,
                ],
            ]
        ];
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
        try {
            $transaction = new Transaction();
            $userId = $request->get('sender_user_id') ?? Auth::user()->id;
            $transaction['sender_user_id'] = $userId;
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

                if ($redirectLink == null) {
                    $transaction['status'] = Transaction::DECLINED_STATUS;
                    $transaction->save();
                    return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage = __('Failed generating stripe session'));
                }
            }

            if ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                $userAvailableAmount = $this->paymentHandler->getLoggedUserAvailableAmount();

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
                    $postId = $transaction['post_id'];
                    $streamId = $transaction['stream_id'];
                    $messageId = $transaction['user_message_id'];
                    $transactionTitle = '';

                    switch ($transactionType) {
                        case Transaction::TIP_TYPE:
                            $transactionTitle = "Gorjeta no Post de {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::CHAT_TIP_TYPE:
                            $transactionTitle = "Gorjeta no Chat de {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::STREAM_ACCESS:
                            $transactionTitle = "Acesso à Transmissão de {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::POST_UNLOCK:
                            $transactionTitle = "Desbloqueio do Post de {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::MESSAGE_UNLOCK:
                            $transactionTitle = "Desbloqueio de Mensagem de {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
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
                        if (!Auth::user()->cpf && !$request->get('cpf')) {
                            return response()->json([
                                'error' => 'CPF não cadastrado',
                                'message' => 'Cadastre seu CPF para prosseguir!',
                            ], 401);
                        }
                        if (!Auth::user()->cpf && $request->input('cpf')) {
                            $user = User::find(Auth::user()->id);
                            $cleanedCpf = preg_replace('/[.\-]/', '', $request->input('cpf'));

                            $existingUser = User::where('cpf', $cleanedCpf)->first();

                            if ($existingUser) {
                                throw new \Exception('CPF já cadastrado no sistema.');
                            }

                            $user->cpf = $cleanedCpf;
                            $user->save();
                        }
                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] === Transaction::CARD_PROVIDER) {
                        $token = json_decode($request->input('card_token'), true);
                        $card_token = self::createToken($token);
                        $brand = self::getCardBrand($token['card_number']);
                        $res = self::generationCardPayment($card_token, $transactionTitle, Auth::user(), $transaction['amount'], $brand, $transaction);
                        $transaction['status'] = Transaction::APPROVED_STATUS;
                        $transaction['transfer_id'] = $res['id'];
                        $transaction->save();

                        self::handleTransactionNotification($transaction);
                        return response()->json($res, 201);
                    }

                    break;
                case Transaction::DEPOSIT_TYPE:
                    $transaction['recipient_user_id'] = Auth::user()->id;
                    if (!Auth::user()->cpf && !$request->get('cpf')) {
                        return response()->json([
                            'error' => 'CPF não cadastrado',
                            'message' => 'Cadastre seu CPF para prosseguir!',
                        ], 401);
                    }


                    if (!Auth::user()->cpf && $request->input('cpf')) {
                        $user = User::find(Auth::user()->id);
                        $cleanedCpf = preg_replace('/[.\-]/', '', $request->input('cpf'));

                        $existingUser = User::where('cpf', $cleanedCpf)->first();

                        if ($existingUser) {
                            throw new \Exception('CPF já cadastrado no sistema.');
                        }

                        $user->cpf = $cleanedCpf;
                        $user->save();
                    }

                    if ($transaction['payment_provider'] == Transaction::PIX_PROVIDER) {
                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] == Transaction::CARD_PROVIDER) {
                        $transactionTitle = "Depósito de R$" . number_format($transaction['amount'], 2, ',', '.') . " para a conta " . Auth::user()->username;

                        $token = json_decode($request->input('card_token'), true);
                        $card_token = self::createToken($token);
                        $brand = self::getCardBrand($token['card_number']);
                        $res = self::generationCardPayment($card_token, $transactionTitle, Auth::user(), $transaction['amount'], $brand, $transaction);
                        $transaction['status'] = Transaction::APPROVED_STATUS;
                        $transaction['transfer_id'] = $res['id'];
                        $transaction->save();

                        DB::table('wallets')
                            ->where('user_id', $transaction['recipient_user_id'])
                            ->increment('total', $transaction['amount']);
                        self::handleTransactionNotification($transaction);
                        return response()->json($res, 201);
                    }

                    break;
                case Transaction::ONE_MONTH_SUBSCRIPTION:
                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                case Transaction::YEARLY_SUBSCRIPTION:
                    $transactionTitle = '';
                    switch ($transactionType) {
                        case Transaction::ONE_MONTH_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 1 Mês para {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::THREE_MONTHS_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 3 Meses para {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::SIX_MONTHS_SUBSCRIPTION:
                            $transactionTitle = "Assinatura de 6 Meses para {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
                            break;
                        case Transaction::YEARLY_SUBSCRIPTION:
                            $transactionTitle = "Assinatura Anual para {$recipientUser->username} - R$" . number_format($transaction['amount'], 2, ',', '.');
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
                        if (!Auth::user()->cpf && !$request->get('cpf') && !$request->get('visitor_id')) {
                            return response()->json([
                                'error' => 'CPF não cadastrado',
                                'message' => 'Cadastre seu CPF para prosseguir!',
                            ], 401);
                        }
                        if (!Auth::user()->cpf && $request->input('cpf') && !$request->get('visitor_id')) {
                            $user = User::find(Auth::user()->id);
                            $cleanedCpf = preg_replace('/[.\-]/', '', $request->input('cpf'));

                            $existingUser = User::where('cpf', $cleanedCpf)->first();

                            if ($existingUser) {
                                throw new \Exception('CPF já cadastrado no sistema.');
                            }

                            $user->cpf = $cleanedCpf;
                            $user->save();
                        }
                        return $this->processPixPayment($transaction);
                    }

                    if ($transaction['payment_provider'] === Transaction::CARD_PROVIDER) {
                        $token = json_decode($request->input('card_token'), true);
                        $card_token = self::createToken($token);
                        $brand = self::getCardBrand($token['card_number']);
                        $res = self::generationCardPayment($card_token, $transactionTitle, Auth::user(), $transaction['amount'], $brand, $transaction);
                        $transaction['status'] = Transaction::APPROVED_STATUS;
                        $transaction['transfer_id'] = $res['id'];
                        $transaction->save();

                        DB::table('wallets')
                            ->where('user_id', $transaction['recipient_user_id'])
                            ->increment('total', $transaction['amount']);
                        $this->paymentHandler->generateSubscriptionByTransaction($transaction);
                        return response()->json($res, 201);
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

        if (isset($redirectLink) && in_array($transaction['payment_provider'], Transaction::ALLOWED_PAYMENT_PROVIDERS)) {
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
            $transaction = Transaction::query()
                ->where('transfer_id', $txid)
                ->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transação não encontrada'], 404);
            }

            if (!$transaction->visitor_id) {
                $transaction->update([
                    'status' => 'approved',
                    'e2eId' => $e2eid
                ]);
            } else {

                $existingAgreement = Agreement::where(['transaction_id' => $transaction->id])->first();

                if ($transaction->type === Transaction::DEPOSIT_TYPE || intval($transaction->recipient_user_id) === intval($transaction->sender_user_id) || $transaction->amount <= 0 || $existingAgreement) {
                    return;
                }

                $recipient = User::query()->where('id', (int) $transaction->recipient_user_id)->first();

                $percentage_reward = floatval(getSetting('referrals.fee_percentage')) ?? 5;
                $discount_reward = floatval($percentage_reward  * ($transaction->amount / 100));

                $referralCodeUsed = ReferralCodeUsage::where(['used_by' => $recipient->id])->first();
                $indicator = null;
                if ($referralCodeUsed) {
                    $indicator = User::where(['referral_code' => $referralCodeUsed->referral_code])->first();
                }
                $percentage_agreement = +$recipient->discount;
                $discount_agreement = floatval($transaction->amount * ($percentage_agreement / 100)) ?? 0;

                $amount_agreement = $discount_agreement;
                if ($indicator) {
                    $amount_agreement = floatval($discount_agreement - $discount_reward);
                }

                $data = [
                    'user_id' => $recipient->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount_agreement,
                    'percentage' => (int) $recipient->discount,
                    'currency' => SettingsServiceProvider::getAppCurrencyCode(),
                ];

                if ($transaction->payment_provider !== Transaction::CREDIT_PROVIDER) {
                    Wallet::query()
                        ->where('user_id', $recipient->id)
                        ->increment('total', $transaction->amount  - $discount_agreement);
                }

                Agreement::create($data);

                if (getSetting('referrals.enabled') && $indicator) {
                    $existingReward = Reward::where(['transaction_id' => $transaction->id])->first();
                    if ($existingReward) return;

                    if (getSetting('referrals.apply_for_months') && intval(getSetting('referrals.apply_for_months')) > 0) {
                        $expiryDatetime = new \DateTime('-' . intval(getSetting('referrals.apply_for_months')) . ' months');
                        if ($expiryDatetime >= $referralCodeUsed->created_at) {
                            return;
                        }
                    }

                    $totalEarnedByUser = 0;
                    if (getSetting('referrals.fee_limit') && intval(getSetting('referrals.fee_limit')) > 0) {
                        $totalEarnedByUser = UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers($indicator->id, $recipient->id);
                        if ($totalEarnedByUser >= floatval(getSetting('referrals.fee_limit'))) {
                            return;
                        }
                    }

                    if ($discount_reward + $totalEarnedByUser >= floatval(getSetting('referrals.fee_limit')) || $discount_reward === 0) {
                        return;
                    }
                    Wallet::query()
                        ->where('user_id', $indicator->id)
                        ->increment('total', $discount_reward);

                    Reward::create([
                        'from_user_id' => $recipient->id,
                        'to_user_id' => $indicator->id,
                        'reward_type' => Reward::FEE_PERCENTAGE_REWARD_TYPE,
                        'transaction_id' => $transaction->id,
                        'referral_code_usage_id' => $referralCodeUsed->id,
                        'amount' => $discount_reward,
                    ]);
                }
                Transaction::query()
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'approved',
                        'e2eId' => $e2eid,
                    ]);

                $botToken = env('BOT_ID');
                $tokenData = 'u' . $transaction->visitor_id . '&%&' . $transaction->visitor_id;
                $token = Crypt::encryptString($tokenData);
                $username = 'u' . $transaction->visitor_id;

                $url = "https://snapic.com.br/beatrizchaves?token=$token";

                $message1 = "Amor, PARÁBENS🥳, Você acabou de assinar minha plataforma de conteúdo por 1 mês, Vou te mandar seus acessos😈";
                $message2 = "Amor, Tenho certeza que vai amar❤️, Está aqui o seu link de acesso👇🏻";
                $message3 = "
Caso queira acessar outra vez, coloque esse acesso, amor👇🏻

**USERNAME:** *{$username}*
**SENHA:** *{$transaction->visitor_id}*";

                $client = new \GuzzleHttp\Client();

                $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'form_params' => [
                        'chat_id' => $transaction->visitor_id,
                        'text' => $message1
                    ],
                ]);

                sleep(2);

                $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'form_params' => [
                        'chat_id' => $transaction->visitor_id,
                        'text' => $message2,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => '⭐ Acessar página ⭐',
                                        'url' => $url,
                                    ],
                                ],
                            ],
                        ]),
                    ],
                ]);
                sleep(2);

                $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'form_params' => [
                        'chat_id' => $transaction->visitor_id,
                        'text' => $message3,
                        'parse_mode' => 'MarkdownV2',
                    ],
                ]);
            }

            if ($transaction->ad || $transaction->visitor_id) {
                $this->pixelService->registerPurchase($transaction->amount, "Purchase");
            }

            if (
                $transaction->type === Transaction::ONE_MONTH_SUBSCRIPTION  ||
                $transaction->type === Transaction::THREE_MONTHS_SUBSCRIPTION ||
                $transaction->type === Transaction::SIX_MONTHS_SUBSCRIPTION ||
                $transaction->type === Transaction::YEARLY_SUBSCRIPTION
            ) {
                $subscription = $this->paymentHandler->generateSubscriptionByTransaction($transaction);
            }

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
