<?php


namespace Sanf\Core\Modules\Project;


use MyCLabs\Enum\Enum;

/**
 * Class ProjectStatus
 * @package NbsPhp\Core\Enum
 */
class ProjectStatus extends Enum
{
    const WAITING_APPROVAL = 10;
    const REJECTED = 20;
    const PUBLISHED = 30;
    const UNPUBLISHED = 40;
}
