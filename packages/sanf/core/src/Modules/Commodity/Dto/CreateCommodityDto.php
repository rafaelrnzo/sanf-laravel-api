<?php


namespace Sanf\Core\Modules\Commodity\Dto;


use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class CreateCommodityDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $title;
    public string $description;
    public ?string $imageFile;
    public string $locationId;
    public ?array $locationMetadata;
    public string $phoneNumber;
    public ?string $whatsappNumber;
    public string $businessEmail;
}
