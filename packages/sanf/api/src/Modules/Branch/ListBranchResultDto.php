<?php


namespace Sanf\Api\Modules\Branch;


use Spatie\DataTransferObject\DataTransferObject;

class ListBranchResultDto extends DataTransferObject
{
    /** @var \Illuminate\Database\Eloquent\Collection|static[] */
    public $list;
}
