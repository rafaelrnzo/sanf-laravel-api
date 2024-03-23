<?php

namespace Sanf\Api\Modules\Product;

use Spatie\DataTransferObject\DataTransferObject;

class ListProductResultDto extends DataTransferObject
{
    /** @var \Illuminate\Database\Eloquent\Collection|static[] */
    public $list;

}
