<?php


namespace Sanf\Api\Modules\Shareholder;


use Spatie\DataTransferObject\DataTransferObject;

class GetListShareholderDto extends DataTransferObject
{
    public string $xid;
}