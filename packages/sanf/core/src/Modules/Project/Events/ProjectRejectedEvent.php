<?php


namespace Sanf\Core\Modules\Project\Events;


class ProjectRejectedEvent extends AbstractProjectEvent
{
    public $oldProject;
    public $user;

    public function __construct($newProject, $oldProject, $user)
    {
        parent::__construct($newProject);
        $this->oldProject = $oldProject;
        $this->user = $user;
    }
}
