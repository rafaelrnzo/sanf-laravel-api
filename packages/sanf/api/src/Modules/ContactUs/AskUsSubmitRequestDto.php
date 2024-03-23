<?php

namespace Sanf\Api\Modules\ContactUs;

use Spatie\DataTransferObject\DataTransferObject;

class AskUsSubmitRequestDto extends DataTransferObject
{
    public int $topic_id;

    public string $title;

    public string $message;

    public string $name;

    public string $phone_number;

    public string $email;

    public string $contract_no;

    public string $contact_media;

    public string $contact_time;

    public ?array $images;
}
