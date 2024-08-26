<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ExecuteQueryJob;

class ScheduleQueryJob extends Command
{
    protected $signature = 'job:schedule-query';
    protected $description = 'Dispatch the job to execute SQL query';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ExecuteQueryJob::dispatch();
    }
}
