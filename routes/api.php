<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas da API para sua aplicação. Essas
| rotas são carregadas pelo RouteServiceProvider dentro de um grupo que
| é atribuído ao grupo de middleware "api". Aproveite para construir sua API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
