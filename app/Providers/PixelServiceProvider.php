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
                        "em" => hash('sha256', Auth::user()->email),
                        "ph" => hash('sha256', Auth::user()->phone),
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

        $data = [
            "data" => [
                [
                    "event_name" => "Purchase",  // Nome do evento (ex: 'Purchase' para uma compra)
                    "event_time" => time(),  // Hora do evento em timestamp (segundos desde a época Unix)
                    "user_data" => [
                        "em" => hash('sha256', 'exemplo@email.com'),  // Endereço de e-mail do usuário (obrigatório hash SHA256)
                        "ph" => hash('sha256', '5511999999999'),  // Número de telefone do usuário (obrigatório hash SHA256)
                        "client_ip_address" => "254.254.254.254",  // Endereço IP do cliente
                        "client_user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0",  // User agent do navegador ou dispositivo
                        "fn" => "João",  // Nome do usuário (opcional)
                        "ln" => "Silva",  // Sobrenome do usuário (opcional)
                        "country" => "BR",  // País do usuário (opcional)
                        "zip" => "12345-678",  // CEP do usuário (opcional)
                        "city" => "São Paulo",  // Cidade do usuário (opcional)
                        "state" => "SP",  // Estado do usuário (opcional)
                        "external_id" => "usuario1234",  // ID externo do usuário (opcional)
                        "db" => "19900101",  // Data de nascimento no formato YYYYMMDD (opcional)
                        "ge" => "M",  // Gênero (opcional, "M" para masculino, "F" para feminino)
                    ],
                    "custom_data" => [
                        "currency" => "BRL",  // Moeda da transação (ex: "BRL" para real brasileiro)
                        "value" => 150.00,  // Valor da transação ou compra
                        "content_ids" => ["1234", "5678"],  // IDs dos produtos comprados (opcional)
                        "content_type" => "product",  // Tipo de conteúdo (exemplo: "product")
                        "content_name" => "Produto XYZ",  // Nome do produto (opcional)
                        "content_category" => "Categoria A",  // Categoria do produto (opcional)
                        "quantity" => 2,  // Quantidade de itens comprados (opcional)
                        "order_id" => "order1234",  // ID do pedido (opcional)
                        "transaction_id" => "txn1234",  // ID de transação (opcional)
                        "shipping" => 10.00,  // Valor do frete (opcional)
                        "tax" => 5.00,  // Valor do imposto (opcional)
                    ],
                    "event_source_url" => "https://seusite.com/finalizar-compra",  // URL da página de origem do evento
                    "action_source" => "website",  // Fonte da ação (ex: "website", "mobile_app", etc.)
                    "event_id" => "12345",  // ID único para o evento (opcional)
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
