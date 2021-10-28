<?php


namespace Sanf\Core\Modules\Financing\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class SendEmailFinancingDto extends DataTransferObject
{
    public ?object $result;
    public ?int $userId;
}
