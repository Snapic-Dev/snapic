<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Este arquivo é onde você pode definir todos os seus comandos de console
| baseados em Closure. Cada Closure é associada a uma instância de comando,
| permitindo uma abordagem simples para interagir com os métodos de IO de cada comando.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
