<?php


namespace Sanf\Api\Modules\Shareholder;


use Spatie\DataTransferObject\DataTransferObject;

class CreateShareholderDto extends DataTransferObject
{

    public string $id;

    public string $title;

    public string $name;

    public string $job;

    public string $percentage;

    public string $type;
}