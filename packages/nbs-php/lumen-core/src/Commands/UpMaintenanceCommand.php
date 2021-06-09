<?php

namespace NbsPhp\Core\Commands;

class UpMaintenanceCommand extends AbstractMaintenanceCommand
{
    /**
     * @var string
     */
    protected $name = 'up';

    /**
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode.';

    /**
     * Bring the application out of maintenance mode.
     */
    public function handle()
    {
        if ($this->maintenance->isDownMode()) {
            $this->setUpMode();
        } else {
            $this->info('The application was already alive.');
        }
    }

    /**
     * Set Application Up Mode.
     *
     * @return void
     * @throws \NbsPhp\Core\Exceptions\FileException
     */
    public function setUpMode()
    {
        $this->maintenance->setUpMode();
        $this->info('Application is now live.');
    }
}
