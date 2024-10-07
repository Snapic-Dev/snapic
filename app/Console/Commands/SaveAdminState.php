<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SaveAdminState extends Command
{

    protected $signature = 'command:name';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Admin panel state saved successfully.');

        return 0;
    }
}
