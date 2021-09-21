<?php


namespace Sanf\Core\Modules\Project\Events;


class ProjectRejectedEvent extends AbstractProjectEvent
{
    public $oldProject;

    public function __construct($newProject, $oldProject)
    {
        parent::__construct($newProject);
        $this->oldProject = $oldProject;
    }
}
