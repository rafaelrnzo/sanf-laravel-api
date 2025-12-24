<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SnapItemDetailPayload extends FlexibleDataTransferObject
{
    public ?string $id;
    public int $price;
    public int $quantity;
    public string $name;
    public ?string $brand;
    public ?string $category;
    public ?string $merchant_name;
    public ?string $url;
}
