<?php


namespace Sanf\Api\Modules\ContactUs;


use Spatie\DataTransferObject\DataTransferObject;

class ListAskUsTopicRequestDto extends DataTransferObject
{
    public int $limit = 10;

    public int $offset = 0;
}