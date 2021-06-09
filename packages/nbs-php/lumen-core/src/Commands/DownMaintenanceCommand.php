<?php

namespace NbsPhp\Core\Commands;

class DownMaintenanceCommand extends AbstractMaintenanceCommand
{
    /**
     * @var string
     */
    protected $name = 'down';

    /**
     * @var string
     */
    protected $description = 'Put the application into maintenance mode.';

    /**
     * Put the application into maintenance mode.
     */
    public function handle()
    {
        if ($this->maintenance->isUpMode()) {
            $this->setDownMode();
        } else {
            $this->info('The application is already in maintenance mode!');
        }
    }

    /**
     * Set Application Down Mode.
     *
     * @return void
     * @throws \NbsPhp\Core\Exceptions\FileException
     */
    public function setDownMode()
    {
        $this->maintenance->setDownMode();
        $this->info('Application is now in maintenance mode.');
    }
}
