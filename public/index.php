<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Registrar o Auto Loader
|--------------------------------------------------------------------------
|
| O Composer fornece um carregador de classes gerado automaticamente para
| nossa aplicação. Apenas precisamos utilizá-lo! Vamos requerê-lo
| no script aqui para que não tenhamos que nos preocupar com o carregamento
| manual de qualquer uma de nossas classes mais tarde. É ótimo relaxar.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Acender as Luzes
|--------------------------------------------------------------------------
|
| Precisamos iluminar o desenvolvimento PHP, então vamos acender as luzes.
| Isso inicializa o framework e o prepara para uso, depois carregará
| esta aplicação para que possamos executá-la e enviar
| as respostas de volta para o navegador e encantar nossos usuários.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Executar a Aplicação
|--------------------------------------------------------------------------
|
| Uma vez que temos a aplicação, podemos lidar com a solicitação
| recebida através do kernel e enviar a resposta associada de volta para
| o navegador do cliente, permitindo que eles desfrutem da aplicação
| criativa e maravilhosa que preparamos para eles.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
