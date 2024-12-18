<?php

namespace App\Observers;

use App\Model\ReferralCodeUsage;
use App\Model\Reward;
use App\Model\Transaction;
use App\Model\Wallet;
use App\Model\Agreement;
use App\Providers\PaymentsServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\UsersServiceProvider;
use App\User;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

class TransactionsObserver
{
    /**
     * Listen to the Transaction deleting event.
     *
     * @param  \App\Model\Transaction  $transaction
     * @return void
     */
    public function deleting(Transaction $transaction)
    {
        // removes invoice along with transaction
        if ($transaction->invoice()) {
            $transaction->invoice()->delete();
        }
    }

    /**
     * Listen to the Transaction created event
     * @param Transaction $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        if ($transaction->status === Transaction::APPROVED_STATUS) {
            $this->discounts($transaction);
        }
    }

    /**
     * Listen to the Transaction updated event
     * @param Transaction $transaction
     * @return void
     */
    public function updating(Transaction  $transaction)
    {
        if ($transaction->getOriginal('status') !== $transaction->status && $transaction->status === Transaction::APPROVED_STATUS) {
            $this->discounts($transaction);
        }
    }


    private function discounts($transaction)
    {
        try {
            if ($transaction->type === Transaction::DEPOSIT_TYPE || intval($transaction->recipient_user_id) === intval($transaction->sender_user_id) || $transaction->amount <= 0) {
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
            $percentage_agreement = $recipient->discount;
            $discount_agreement = floatval($transaction->amount * ($percentage_agreement / 100));

            $existingAgreement = Agreement::where(['transaction_id' => $transaction->id])->first();

            if (!$existingAgreement) {

                $value_of_reward = 0;

                if ($indicator && $indicator->id) {
                    $value_of_reward = 5;
                };
                if ($transaction->payment_provider === "pix") {
                    switch ($percentage_agreement) {
                        case 5:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', floatval(($discount_agreement * 4) - $discount_reward - floatval($transaction->amount / 10)));
                            break;
                        case 10:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', floatval(($discount_agreement * 2.50)  - $discount_reward - floatval($transaction->amount / 10)));
                            break;
                        case 15:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', floatval(($discount_agreement * 2) - $discount_reward - floatval($transaction->amount / 10)));
                            break;
                        case 20:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', floatval(($discount_agreement * 1.75) - $discount_reward - floatval($transaction->amount / 10)));
                            break;
                        case 60:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', abs(floatval(($discount_agreement * 2) - $discount_reward - floatval($transaction->amount / 10))) / 6);
                            break;
                        default:
                            Wallet::query()
                                ->where('user_id', $recipient->id)
                                ->decrement('total', floatval(($discount_agreement * 2) - $discount_reward - floatval($transaction->amount / 10)));
                            break;
                    }
                } else {

                    Wallet::query()
                        ->where('user_id', $recipient->id)
                        ->decrement('total', floatval(($discount_agreement - $discount_reward) - floatval($transaction->amount / 10)));
                }

                $amount = $discount_agreement;
                if ($indicator) {
                    $amount = floatval($discount_agreement - $discount_reward);
                }

                $data = [
                    'user_id' => $recipient->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'percentage' => (int) $recipient->discount,
                    'currency' => SettingsServiceProvider::getAppCurrencyCode(),
                ];

                Agreement::create($data);
            }

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

                Reward::create([
                    'from_user_id' => $recipient->id,
                    'to_user_id' => $indicator->id,
                    'reward_type' => Reward::FEE_PERCENTAGE_REWARD_TYPE,
                    'transaction_id' => $transaction->id,
                    'referral_code_usage_id' => $referralCodeUsed->id,
                    'amount' => $discount_reward,
                ]);
                Wallet::query()
                    ->where('user_id', $indicator->id)
                    ->increment('total', $discount_reward);
                Wallet::query()
                    ->where('user_id',  $recipient->id)
                    ->decrement('total', $discount_reward - $discount_reward);
            }
        } catch (\Exception $exception) {
            Log::log(LogLevel::ERROR, "Failed to generate reward: " . $exception->getMessage());
        }
    }
}
