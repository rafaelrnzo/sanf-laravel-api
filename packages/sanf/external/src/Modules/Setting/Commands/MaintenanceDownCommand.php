<?php

namespace Sanf\External\Modules\Setting\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MaintenanceDownCommand extends Command
{
    protected $name = 'maintenance:down';

    protected $description = 'Make an application into maintenance mode.';

    public function handle()
    {
        sleep(5);

        Artisan::call('down');
    }
}
