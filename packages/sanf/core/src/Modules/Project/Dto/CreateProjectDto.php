<?php


namespace Sanf\Core\Modules\Project\Dto;


use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class CreateProjectDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $title;
    public string $description;
    public int $submissionLimitAt;
    public ?string $imageFile;
    public string $locationId;
    public ?array $locationMetadata;
    public string $phoneNumber;
    public ?string $whatsappNumber;
    public string $businessEmail;
}
