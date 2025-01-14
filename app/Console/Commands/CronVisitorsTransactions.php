<?php

namespace App\Console\Commands;

use App\Helpers\PaymentHelper;
use App\Model\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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


        $transactionsMy = Transaction::whereNotNull('visitor_id')
            ->where('created_at', '<', Carbon::now()->subMinutes(30))
            ->where('status', 'pending')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('transactions')
                    ->groupBy('sender_user_id');
            })
            ->get();


        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);

        foreach ($transactionsMy as $transactionData) {
            $transaction = new Transaction();
            $transaction['sender_user_id'] = $transactionData['sender_user_id'];
            $transaction['recipient_user_id'] = $transactionData['recipient_user_id'];
            $transaction['type'] = 'one-month-subscription';
            $transaction['status'] = Transaction::PENDING_STATUS_REVALIDATE;
            $transaction['amount'] = 9.90;
            $transaction['currency'] = config('app.site.currency_code');
            $transaction['payment_provider'] = 'pix';
            $transaction['visitor_id'] = $transactionData['visitor_id'];
            $transaction['ad'] = $transactionData['visitor_id'];
            $transaction['visitor_provider'] = 'telegram';

            $res = $this->paymentHelper->generationPixPayment($transaction);
            $transaction['transfer_id'] = $res['txid'];
            $transaction->save();

            $pix = $res['pixCopiaECola'];
            $message1 = "Amor, Promoção Relâmpago das minhas assinaturas só pra você, R$9,90 para ter acesso a um mês inteiro comigo e um chat exclusivo  não perde a chance de me ter na palma da sua mão por um preço de uma coxinha ❤️";
            $message2 = "Esse é o código amor, É SÓ COPIAR E COLAR NO PIX que eu mando o acesso 👇🏻";


            $client->post("https://api.telegram.org/bot7289936162:AAFKDXg3Y8YjnuB9rfteUi8PARLYKj8vbvM/sendMessage", [
                'form_params' => [
                    'chat_id' => '8028490948',
                    'text' => $message1,
                ],
            ]);

            $client->post("https://api.telegram.org/bot7289936162:AAFKDXg3Y8YjnuB9rfteUi8PARLYKj8vbvM/sendMessage", [
                'form_params' => [
                    'chat_id' => $transactionData['visitor_id'],
                    'text' => $message2,
                ],
            ]);

            $client->post("https://api.telegram.org/bot7289936162:AAFKDXg3Y8YjnuB9rfteUi8PARLYKj8vbvM/sendMessage", [
                'form_params' => [
                    'chat_id' => $transactionData['visitor_id'],
                    'text' => $pix,
                ],
            ]);
        }


        Log::channel('cronjobs')->info('[*][' . date('H:i:s') . "] Finished processing subscriptions renew.\r\n");
        return 0;
    }
}
