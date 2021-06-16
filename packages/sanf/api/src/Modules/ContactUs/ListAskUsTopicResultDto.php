<?php


namespace Sanf\Api\Modules\ContactUs;


use Spatie\DataTransferObject\DataTransferObject;

class ListAskUsTopicResultDto extends DataTransferObject
{
    /** @var \Illuminate\Database\Eloquent\Collection|static[] */
    public $list;
}