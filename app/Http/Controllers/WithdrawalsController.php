<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Http\Requests\CreateWithdrawalRequest;
use App\Model\Withdrawal;
use App\Providers\EmailsServiceProvider;
use App\Providers\GenericHelperServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\StripeServiceProvider;
use App\Providers\WithdrawalsServiceProvider;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Stripe\Payout;

class WithdrawalsController extends Controller
{
    /**
     * Method used for requesting an withdrawal request from the admin.
     *
     * @param CreateWithdrawalRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    protected $paymentHandler;

    /**
     * PaymentsController constructor.
     * @param PaymentsServiceProvider $paymentsProvider
     */
    public function __construct(PaymentHelper $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    public function requestWithdrawal(CreateWithdrawalRequest $request)
    {
        try {
            $minimal = 200;
            $amount = $request->request->get('amount');
            $message = $request->request->get('message');
            $identifier = $request->request->get('identifier');

            $data = [
                'user_id' => Auth::user()->id,
                'amount' => floatval($amount),
                'message' => $message,
                'payment_identifier' => $identifier,
                'status' => Withdrawal::REQUESTED_STATUS,
                'e2eId' => null,
                'transfer_id' => null
            ];
            $user = Auth::user();

            if ($amount != null && $user != null) {

                if ($user->wallet == null) {
                    $user->wallet = GenericHelperServiceProvider::createUserWallet($user);
                }

                if (floatval($amount) === floatval(PaymentsServiceProvider::getWithdrawalMinimumAmount()) && floatval($amount) > $user->wallet->total) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => __("Você não tem crédito suficiente para sacar. O valor mínimo é: ", ['minAmount' => PaymentsServiceProvider::getWithdrawalMinimumAmount()])
                        ]
                    );
                }

                if (floatval($amount) > $user->wallet->total) {
                    return response()->json(['success' => false, 'message' => __('Você não pode sacar esse valor, tente um valor menor')]);
                }

                $fee = 0;
                if (getSetting('payments.withdrawal_allow_fees') && floatval(getSetting('payments.withdrawal_default_fee_percentage')) > 0) {
                    $fee = (floatval(getSetting('payments.withdrawal_default_fee_percentage')) / 100) * floatval($amount);
                }


                if (floatval($minimal) > floatval($amount)) {
                    $res =  $this->paymentHandler->makeTransfer('aaa');

                    if (!array_key_exists('STATUS', $res) || $res['STATUS'] !== 'EM_PROCESSAMENTO') {
                        switch ($res['nome']) {
                            case 'valor_invalido':
                                throw new Exception("A chave Pix fornecida é inválida");
                                break;
                            default:
                                throw new Exception("Erro desconhecido");
                                break;
                        }
                    }

                    $data['status'] = Withdrawal::APPROVED_STATUS;
                    $data['e2eId'] = $res['e2eId'];
                    $data['transfer_id'] = $res['idEnvio'];
                }

                Withdrawal::create($data);
                $user->wallet->update([
                    'total' => $user->wallet->total - floatval($amount),
                ]);

                $totalAmount = number_format($user->wallet->total, 2, '.', '');
                $pendingBalance = number_format($user->wallet->pendingBalance, 2, '.', '');

                $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
                foreach ($adminEmails as $user) {
                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $user->email,
                            'subject' => __('Ação necessária | Nova solicitação de saque'),
                            'title' => __('Olá, :name,', ['name' => $user->name]),
                            'content' => __('Há uma nova solicitação de saque em :siteName que requer sua atenção.', ['siteName' => getSetting('site.name')]),
                            'button' => [
                                'text' => __('Ir para o admin'),
                                'url' => route('voyager.dashboard') . '/withdrawals',
                            ],
                        ]
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => __('Saque solicitado com sucesso'),
                    'totalAmount' => SettingsServiceProvider::getWebsiteFormattedAmount($totalAmount),
                    'pendingBalance' => SettingsServiceProvider::getWebsiteFormattedAmount($pendingBalance),
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
        return response()->json(['success' => false, 'message' => __('Algo deu errado, por favor, tente novamente')], 500);
    }


    public function onboarding()
    {
        $user = Auth::user();

        try {
            // redirect user to the form where he must add his details for the first time
            $onboardingType = "account_onboarding";
            // check if user have a stripe account created
            if (!$user->stripe_account_id) {
                WithdrawalsServiceProvider::createStripeAccountForUser($user);
            }

            // check if user done onboarding and if so just redirect him to only update his details
            if (WithdrawalsServiceProvider::userDoneStripeOnboarding($user)) {
                $onboardingType = "account_update";
            }

            // create account link (Stripe hosted UI to complete verification / onboarding process)
            $accountLink = StripeServiceProvider::createStripeAccountLink($user->stripe_account_id, $onboardingType);
        } catch (\Exception $exception) {
            Log::channel('withdrawals')->error(
                'StripeConnect onboarding failed being initiated',
                ['error' => $exception->getMessage(), 'userId' => $user->id]
            );
            return back()->with('error', __('Onboarding initiation failed, please retry or contact support'));
        }

        // redirect on Stripe hosted UI
        return Redirect::away($accountLink->url);
    }

    public function approveWithdrawal($withdrawalId)
    {
        // Busca o registro de saque pelo ID e carrega também o usuário relacionado.
        $withdrawal = Withdrawal::query()->where('id', $withdrawalId)->with('user')->first();

        // Verifica se o saque existe. Se não existir, retorna uma resposta JSON com erro 404.
        if (!$withdrawal) {
            return response()->json(['success' => false, 'error' => __('Saque não encontrado')], 404);
        }

        // Verifica se o saque já foi processado. Se sim, retorna uma resposta JSON informando que o saque já foi processado.
        if ($withdrawal->status !== Withdrawal::REQUESTED_STATUS) {
            return response()->json(['success' => false, 'error' => __('Saque já processado')], 400);
        }

        try {
            // Variável para rastrear se a operação de pagamento foi bem-sucedida.
            $payoutSucceeded = true;

            // Verifica se o método de pagamento é "Stripe Connect".
            if ($withdrawal->payment_method === 'Stripe Connect') {
                $payoutSucceeded = false;

                // Transfere dinheiro para a conta conectada, caso ainda não tenha sido transferido.
                if (!$withdrawal->stripe_transfer_id) {
                    $transfer = StripeServiceProvider::createConnectedAccountTransfer($withdrawal, $withdrawal->user->stripe_account_id);
                    $withdrawal->stripe_transfer_id = $transfer->id;

                    // Salva o registro de saque após a transferência, para garantir que a transferência já foi feita caso algo dê errado com o pagamento.
                    $withdrawal->save();
                }

                // Cria o pagamento manual.
                $payout = StripeServiceProvider::createManualPayout($withdrawal->user->stripe_account_id);
                $withdrawal->stripe_payout_id = $payout->id;

                // Verifica o status do pagamento.
                if ($payout->status === Payout::STATUS_PAID) {
                    $payoutSucceeded = true;
                }

                // Se o pagamento falhar, atualiza o status do saque para "Rejeitado".
                if ($payout->status === Payout::STATUS_FAILED) {
                    $withdrawal->status = Withdrawal::REJECTED_STATUS;
                }

                // Salva o registro de saque atualizado.
                $withdrawal->save();
            }

            // Só atualiza o status do saque para "Aprovado" se o pagamento foi bem-sucedido (quando o método de pagamento é Stripe Connect).
            // Caso contrário, deixa o webhook decidir.
            if ($payoutSucceeded) {
                $withdrawal->status = Withdrawal::APPROVED_STATUS;
                $withdrawal->save();
            }
        } catch (\Exception $exception) {
            // Captura qualquer exceção e retorna uma resposta JSON com a mensagem de erro.
            return response()->json(['success' => false, 'error' => 'Erro: "' . $exception->getMessage() . '"'], 500);
        }

        // Define a mensagem de sucesso, dependendo se o pagamento foi concluído com sucesso ou apenas iniciado.
        $message = $payoutSucceeded ? __("Saque aprovado com sucesso") : __("Pagamento do saque iniciado");

        // Retorna uma resposta JSON indicando sucesso e a mensagem apropriada.
        return response()->json(['success' => true, 'message' => $message]);
    }


    public function rejectWithdrawal($withdrawalId)
    {
        $withdrawal = Withdrawal::query()->where('id', $withdrawalId)->first();

        if (!$withdrawal) {
            return response()->json(['success' => false, 'error' => __('Withdrawal not found')], 404);
        }

        if ($withdrawal->status !== Withdrawal::REQUESTED_STATUS) {
            return response()->json(['success' => false, 'error' => __('Withdrawal already processed')], 400);
        }

        try {
            $withdrawal->status = Withdrawal::REJECTED_STATUS;
            $withdrawal->save();
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'error' => 'Error: "' . $exception->getMessage() . '"'], 500);
        }

        return response()->json(['success' => true, 'message' => __("Withdrawal rejected successfully")]);
    }
}
