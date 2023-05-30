<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class AddToCartRequestDto extends ScaninaRequestDataTransferObject
{
    public string $email;
    public string $typeId;
    public string $productId;
    public ?int $rentStartDate;
    public ?int $rentEndDate;
    public ?int $quantity;
    public ?int $serviceDate;
    public ?string $notes;
}
