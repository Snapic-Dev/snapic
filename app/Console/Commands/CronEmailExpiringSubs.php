<?php

namespace App\Console\Commands;

use App\Model\Subscription;
use App\Providers\EmailsServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CronEmailExpiringSubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:email_expiring_subs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emails users about soon to expire subs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Emails users about soon to expire subs.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."]  Starting: Expiring email subs..\r\n");

        // Subs to ne re-newed in the upcoming 24h
        $renewalSubs = Subscription::with('subscriber', 'creator')
            ->whereRaw('HOUR(TIMEDIFF(expires_at,now() )) <= 24')
            ->whereRaw('expires_at < now() + INTERVAL 24 HOUR')
            ->where('paypal_agreement_id', null)
            ->where('stripe_subscription_id', null)
            ->where('paypal_plan_id', null)
            ->get();

        foreach ($renewalSubs as $subToRenew) {
            if (isset($subToRenew->subscriber->settings['notification_email_expiring_subs']) && $subToRenew->subscriber->settings['notification_email_expiring_subs'] == 'true') {
                App::setLocale($subToRenew->subscriber->settings['locale']);
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $subToRenew->subscriber->email,
                        'subject' => __('Assinatura expirando'),
                        'title' => __('Olá, :subscriberName', ['subscriberName' => $subToRenew->subscriber->name]),
                        'content' => __('Sua assinatura de :creatorName está prestes a expirar nas próximas 24 horas. Por favor, adicione crédito para continuar com sua assinatura.', ['creatorName' => $subToRenew->creator->name]),
                        'button' => [
                            'text' => __('Gerenciar suas assinaturas'),
                            'url' => route('my.settings', ['type' => 'subscriptions']),
                        ],
                    ]
                );
            }
        }

        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."]  Expiring email subs sent successfully..\r\n");
        return 0;
    }
}
