<?php

namespace App\Providers;


use App\Model\Attachment;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Reaction;
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

        if ($date == "") {
            $date = date('Y-m-d');
        }

        $query = Post::whereDate('created_at', $date);
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

        if ($date == "") {
            $date = date('Y-m-d');
        }

        $query = Subscription::query()
            ->whereDate('created_at', $date)
            ->where('expires_at', '>=', new \DateTime('now', new \DateTimeZone('UTC')));


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

        if ($date == "") {
            $date = date('Y-m-d');
        }

        $query = User::whereDate('created_at', $date);

        return $query->count();
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

        if ($date == "") {
            $date = date('Y-m-d');
        }

        $query = Transaction::query()
            ->where('status', '=', Transaction::APPROVED_STATUS)
            ->where('type', '=', Transaction::DEPOSIT_TYPE)
            ->whereDate('created_at', $date);


        return $query->sum('amount');
    }

    public static function influencerAmount() /*dash*/
    {

        $date = request()->query('date');

        if ($date == "") {
            $date = date('Y-m-d');
        }

        return User::query()
            ->where('paid_profile', 1)
            ->whereDate('created_at', $date)
            ->count();
    }

    public static function topInfluencerList()
    {
        $topInfluencers = User::where('paid_profile', 1)
            ->select('users.*', DB::raw('COALESCE(SUM(CASE WHEN transactions.status = \'approved\' THEN transactions.amount ELSE 0 END), 0) as total_earned'))
            ->leftJoin('transactions', 'users.id', '=', 'transactions.recipient_user_id')
            ->groupBy('users.id')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

        return $topInfluencers;
    }

    public static function getSubscriberRank($senderID)
    {
        $subscribers = Subscription::where('recipient_user_id', $senderID)
            ->where('expires_at', '>', Carbon::now('UTC'))
            ->count();

        return $subscribers;
    }

    public static function comissionPaid()
    {
        $date = request()->query('date');

        if ($date == "") {
            $date = date('Y-m-d');
        }

        // Cria a consulta para calcular o total dos valores retirados
        $totalAmount = Withdrawal::whereIn('user_id', function ($query) use ($date) {
            $query->select('id')
                ->from('users')
                ->where('paid_profile', true)
                ->where('status', 'approved')
                ->whereDate('created_at', $date);
        })
            ->sum('amount');

        return $totalAmount;
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
