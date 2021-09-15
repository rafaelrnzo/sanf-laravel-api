<?php


namespace Sanf\Core\Modules\Location\Internal;


class LocationRequestDto
{

    public $level;

    public $parent_id = null;

    public $name = null;

    public $sort_by = 'name';

    public $offset = 0;

    public $limit = null;
}