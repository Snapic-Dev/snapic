<?php

namespace App\Providers;

use App\Model\Agreement;
use App\Model\Attachment;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Reaction;
use App\Model\Reward;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\Wallet;
use Illuminate\Http\Request;
use App\Model\Withdrawal;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DashboardServiceProvider extends ServiceProvider
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
    public function boot() {}

    /**
     * Get admin dashboard total posts count
     * @return int
     */
    public static function getPostsCount() /*dash*/
    {
        $date = request()->query('date');

        $query = Post::when($date, function ($query, $date) {
            return $query->whereDate('created_at', $date);
        });

        return $query->count();
    }

    /**
     * Get admin dashboard total post attachments count
     * @return int
     */
    public static function getPostAttachmentsCount()
    {
        return Attachment::query()->whereNotNull('post_id')->count();
    }

    /**
     * Get admin dashboard total post comments count
     * @return int
     */
    public static function getPostCommentsCount()
    {
        return PostComment::all()->count();
    }

    /**
     * Get admin dashboard total reactions count
     * @return int
     */
    public static function getReactionsCount()
    {
        return Reaction::query()->whereNotNull('post_id')->count();
    }

    /**
     * Get admin dashboard active subscriptions count
     * @return int
     * @throws \Exception
     */
    public static function getActiveSubscriptionsCount() /*dash*/
    {
        $date = request()->query('date');

        $query = Subscription::query()
            ->when($date, function ($query, $date) {
                return $query->whereDate('expires_at', '>=', $date)->whereDate('created_at', '<=', $date);
            })
            ->whereColumn('expires_at', '>', 'created_at')
            ->where('status', 'completed');
        return $query->count();
    }

    /**
     * Get admin dashboard total transactions count
     * @return int
     */
    public static function getTotalTransactionsCount()
    {
        return Transaction::all()->count();
    }

    /**
     * Get admin dashboard last 24 hours registered users count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursRegisteredUsersCount() /*dash*/
    {
        $date = request()->query('date');
        $query = User::when($date, function ($query, $date) {
            return $query->whereDate('created_at', $date);
        })->count();

        return $query;
    }

    /**
     * Get admin dashboard last 24 hours total earned
     * @return mixed
     * @throws \Exception
     */
    public static function getLast24HoursTotalEarned()
    {
        return Transaction::query()
            ->where([
                ['created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC'))],
                ['status', '=', Transaction::APPROVED_STATUS]
            ])
            ->whereNotIn('type', [Transaction::DEPOSIT_TYPE, Transaction::WITHDRAWAL_TYPE])
            ->sum('amount');
    }

    /**
     * Get admin dashboard last 24 hours subscriptions count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursSubscriptionsCount()
    {
        return Subscription::query()->where('created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard last 24 hours posts count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursPostsCount()
    {
        return Post::query()->where('created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard total subscriptions revenue
     * @return mixed
     * @throws \Exception
     */
    public static function getTotalSubscriptionsRevenue()
    {
        return Transaction::query()->where('status', '=', Transaction::APPROVED_STATUS)->whereNotNull('subscription_id')->sum('amount');
    }

    /**
     * Get admin dashboard total earned
     * @return mixed
     */
    public static function getTotalEarned() /*dash*/
    {
        $date = request()->query('date');

        $query = Transaction::where('status', Transaction::APPROVED_STATUS)
            ->whereIn('payment_provider', ['pix', 'card'])
            ->when($date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->sum('amount');

        return
            number_format($query, 2, ',', '.');
    }

    public static function influencerAmount() /*dash*/
    {
        $date = request()->query('date');

        return User::query()
            ->where('role_id', 3)
            ->when($date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->count();
    }

    public static function topInfluencerList()
    {
        $topInfluencers = User::where('role_id', 3)->whereNotNull('identity_verified_at')
            ->leftJoin(DB::raw('
        (SELECT recipient_user_id, SUM(CASE 
            WHEN status = "approved" AND type != "deposit" 
            THEN amount ELSE 0 END) as total_transactions 
        FROM transactions 
        GROUP BY recipient_user_id) as t'), 'users.id', '=', 't.recipient_user_id')
            ->leftJoin(DB::raw('
        (SELECT to_user_id, SUM(amount) as total_rewards 
        FROM rewards 
        GROUP BY to_user_id) as r'), 'users.id', '=', 'r.to_user_id')
            ->select('users.id', 'users.username', 'users.email', DB::raw('
        COALESCE(t.total_transactions, 0) + COALESCE(r.total_rewards, 0) as total_earned
    '))
            ->groupBy('users.id', 'users.username', 'users.email', 't.total_transactions', 'r.total_rewards')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

        return $topInfluencers;
    }

    public static function getSubscriberRank($senderID)
    {
        $subscribers = Subscription::where('recipient_user_id', $senderID)
            ->where('expires_at', '>=', Carbon::now('UTC'))
            ->count();

        return $subscribers;
    }

    public static function comissionPaid()
    {
        $date = request()->query('date');

        $rewards = Reward::when($date, function ($query, $date) {
            return $query->whereDate('created_at', $date);
        })->sum('amount');

        $agreements = Agreement::when($date, function ($query, $date) {
            return $query->whereDate('created_at', $date);
        })->sum('amount');

        $transactions = Transaction::where('status', Transaction::APPROVED_STATUS)
            ->where('type', '!=', Transaction::DEPOSIT_TYPE)
            ->when($date, function ($query, $date) {
                return $query->whereDate('transactions.created_at', $date); // Especificando a tabela transactions
            })
            ->join('users', 'transactions.recipient_user_id', '=', 'users.id')
            ->where('users.role_id', 3)
            ->sum('transactions.amount');

        return number_format((float) $rewards + (float) $transactions - $agreements, 2, ',', '.');
    }

    public function getMetrics(Request $request)
    {
        $date = $request->input('date');

        $metrics = [
            'totalEarned' => SettingsServiceProvider::getWebsiteFormattedAmount(DashboardServiceProvider::getTotalEarned($date)),
        ];

        return response()->json($metrics);
    }
}
