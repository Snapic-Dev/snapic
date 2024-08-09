<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\Filesystem;

class CronClearCache extends Command
{
    /**
     * O nome e assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'cron:clear_cache_files';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Clears old session fies, keeping server files quota reduced';

    /**
     * Cria uma nova instância do comando.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Manipula a execução do comando, limpando arquivos de cache antigos.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        $file = new Filesystem;
        $file->cleanDirectory(storage_path('app') . '/tmp');
        $file->cleanDirectory(storage_path('app') . '/chunks');

        Log::channel('cronjobs')->info('[*][' . date('H:i:s') . "] Cached files cleared.\r\n");
        return 0;
    }
}
