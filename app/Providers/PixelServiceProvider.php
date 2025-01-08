<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class PixelServiceProvider extends ServiceProvider
{

    protected $pixel_id;
    protected $pixel_secret;
    public function __construct()
    {
        $this->pixel_id = env('PIXEL_ID');
        $this->pixel_secret = env('PIXEL_SECRET');
    }

    public function registerPurchase($amount, $event_name)
    {
        $eventData = [
            "data" => [
                [
                    "event_name" => $event_name,
                    "event_time" => time(),
                    "user_data" => [
                        "client_ip_address" => $_SERVER['REMOTE_ADDR'],
                        "client_user_agent" => $_SERVER['HTTP_USER_AGENT']
                    ],
                    "custom_data" => [
                        "currency" => "BRL",
                        "value" => $amount,
                    ],
                    "event_source_url" => "https://snapic.com.br/",
                    "action_source" => "website"
                ]
            ]
        ];

        $url = "https://graph.facebook.com/v17.0/{$this->pixel_id}/events";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->pixel_secret}"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eventData));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return true;
        } else {
            return false;
        }
    }
}
