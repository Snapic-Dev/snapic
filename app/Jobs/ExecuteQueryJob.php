<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExecuteQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        DB::statement("
            INSERT INTO `usuarios_premiados` (`ID`, `nome`, `codigo_indicacao`)
            SELECT U.ID, U.name, RU.referral_code
            FROM users U
            JOIN referral_code_usages RU ON RU.user_id = U.id
            LEFT JOIN usuarios_premiados UP ON UP.ID = U.id
            WHERE RU.DHS_CRIACAO > '2023-12-31' AND UP.ID IS NULL;
        ");
    }
}
