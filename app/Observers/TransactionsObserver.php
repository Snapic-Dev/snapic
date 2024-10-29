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
            $this->discountAgreement($transaction);
            $this->createRewardForTransaction($transaction);
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
            $this->discountAgreement($transaction);
            $this->createRewardForTransaction($transaction);
        }
    }

    private function discountAgreement(Transaction $transaction)
    {
        try {
            if ($transaction->type === Transaction::DEPOSIT_TYPE || intval($transaction->recipient_user_id) === intval($transaction->sender_user_id)) {
                return;
            }
            $existingAgreement = Agreement::where(['transaction_id' => $transaction->id])->first();

            if (!$existingAgreement) {
                $recipientUserId = (int) $transaction->recipient_user_id;

                if ($recipientUserId <= 0) {
                    throw new \Exception('Invalid recipient user ID.');
                }

                $recipient = User::query()->where('id', $recipientUserId)->first();
                $discount = floatval($transaction->amount * ($recipient->discount / 100));

                Wallet::query()
                ->where('user_id', $recipientUserId)
                ->decrement('total', floatval($discount  - floatval($transaction->amount /10)) );

                $data = [
                    'user_id' => $recipientUserId,
                    'transaction_id' => $transaction->id,
                    'amount' => $discount,
                    'percentage' => (int) $recipient->discount,
                    'currency' => SettingsServiceProvider::getAppCurrencyCode(),
                ];

                Agreement::create($data);
                return;
            }
        } catch (\Exception $e) {
            dd(LogLevel::ERROR, "Failed to apply discount agreement: " . $e->getMessage());
        }
    }

    private function createRewardForTransaction($transaction)
    { 
        if (getSetting('referrals.enabled')) {
            try {
            $percentage = floatval(getSetting('referrals.fee_percentage')) ?? 5;
            $existingReward = Reward::where(['transaction_id' => $transaction->id])->first();
            if($existingReward) return;

            $referralCodeUsage = ReferralCodeUsage::where(['used_by' => $transaction->recipient_user_id])->first();
            $referralCodeUser = User::where(['referral_code' => $referralCodeUsage->referral_code])->first();
            if(!$referralCodeUsage ||!$referralCodeUsage ) return;

            if (getSetting('referrals.apply_for_months') && intval(getSetting('referrals.apply_for_months')) > 0) {
                $expiryDatetime = new \DateTime('-' . intval(getSetting('referrals.apply_for_months')) . ' months');
                if ($expiryDatetime >= $referralCodeUsage->created_at) {
                    return;
                }
            }
            $totalEarnedByUser = 0;
            if (getSetting('referrals.fee_limit') && intval(getSetting('referrals.fee_limit')) > 0) {
                $totalEarnedByUser = UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers($referralCodeUser->id, $transaction->recipient_user_id);
                if ($totalEarnedByUser >= floatval(getSetting('referrals.fee_limit'))) {
                    return;
                }
            }

            if ($transaction->amount <= 0) {
                return;
            }

            $rewardFee = floatval($percentage  * ($transaction->amount / 100));
            if ($rewardFee + $totalEarnedByUser >= floatval(getSetting('referrals.fee_limit')) || $rewardFee === 0) {
                return;
            }
            Reward::create([
                'from_user_id' => $transaction->recipient_user_id,
                'to_user_id' => $referralCodeUser->id,
                'reward_type' => Reward::FEE_PERCENTAGE_REWARD_TYPE,
                'transaction_id' => $transaction->id,
                'referral_code_usage_id' => $referralCodeUsage->id,
                'amount' => $rewardFee,
            ]);

            Wallet::query()
            ->where('user_id', $referralCodeUser->id)
            ->increment('total', $rewardFee);

            Wallet::query()
            ->where('user_id', $referralCodeUsage->id)
            ->decrement('total', $rewardFee);
        } catch (\Exception $exception) {
            Log::log(LogLevel::ERROR, "Failed to generate reward: " . $exception->getMessage());
        }

        }
    }
}
