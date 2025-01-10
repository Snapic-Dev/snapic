<?php

namespace App\Console\Commands;

use App\Helpers\PaymentHelper;
use App\Model\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronVisitorsTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:visitors_transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process subscriptions renewal visitors (update status, add/remove credit, etc)';

    public $paymentHelper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
        parent::__construct();
    }

    /**
     * Process subscriptions renewal (update status, add/remove credit, etc).
     *
     * @return mixed
     */
    public function handle()
    {
        Log::channel('cronjobs')->info('[*][' . date('H:i:s') . "] Processing expired subscriptions.\r\n");

        $transaction = new Transaction();
        $transaction['sender_user_id'] = 63;
        $transaction['recipient_user_id'] = 9;
        $transaction['type'] = 'one-month-subscription';
        $transaction['status'] = Transaction::PENDING_STATUS;
        $transaction['amount'] = '9.90';
        $transaction['currency'] = config('app.site.currency_code');
        $transaction['payment_provider'] = 'pix';
        $transaction['visitor_id'] = '8028490948';
        $transaction['ad'] = '8028490948';
        $transaction['visitor_provider'] = 'telegram';
        $res = $this->paymentHelper->generationPixPayment($transaction);
        $transaction['transfer_id'] = $res['txid'];
        $transaction->save();

        $pix = $res['pixCopiaECola'];
        $message1 = "teu pix: $pix, descontin";
        $botToken = env('BOT_ID');
        $client = new \GuzzleHttp\Client();

        $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'form_params' => [
                'chat_id' => '8028490948',
                'text' => $message1
            ],
        ]);


        Log::channel('cronjobs')->info('[*][' . date('H:i:s') . "] Finished processing subscriptions renew.\r\n");
        return 0;
    }
}
