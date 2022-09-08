<?php

namespace Sanf\External\Modules\Setting\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MaintenanceUpCommand extends Command
{
    protected $name = 'maintenance:up';

    protected $description = 'Make an application live.';

    public function handle()
    {
        sleep(5);

        Artisan::call('up');
    }
}
