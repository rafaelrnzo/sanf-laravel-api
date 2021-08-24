<?php


namespace Sanf\Api\Modules\Shareholder;


use Spatie\DataTransferObject\DataTransferObject;

class UpdateShareholderDto extends DataTransferObject
{

    public string $id;

    public string $no;

    public string $title;

    public string $name;

    public ?string $job;

    public string $percentage;

    public string $type;
}