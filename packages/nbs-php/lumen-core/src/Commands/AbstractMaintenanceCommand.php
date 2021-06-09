<?php

namespace NbsPhp\Core\Commands;

use Illuminate\Console\Command as IlluminateCommand;
use NbsPhp\Core\Services\MaintenanceModeService;

abstract class AbstractMaintenanceCommand extends IlluminateCommand
{
    /**
     * Maintenance Service.
     *
     * @var MaintenanceModeService
     */
    protected $maintenance;

    /**
     * @param \NbsPhp\Core\Services\MaintenanceModeService $maintenance
     */
    public function __construct(MaintenanceModeService $maintenance)
    {
        parent::__construct();

        $this->maintenance = $maintenance;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    abstract public function handle();

}
