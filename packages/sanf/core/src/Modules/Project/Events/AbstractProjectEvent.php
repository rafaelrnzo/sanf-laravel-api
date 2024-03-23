<?php

namespace Sanf\Core\Modules\Project\Events;

use NbsPhp\Core\Event;

abstract class AbstractProjectEvent extends Event
{
    public $project;

    public function __construct($project)
    {
        $this->project = $project;
    }
}
