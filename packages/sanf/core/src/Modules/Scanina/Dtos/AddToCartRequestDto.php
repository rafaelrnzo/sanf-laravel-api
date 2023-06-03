<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class AddToCartRequestDto extends ScaninaRequestDataTransferObject
{
    public string $email;
    public int $type;
    public string $productXid;
    public ?int $rentStartDate;
    public ?int $rentEndDate;
    public ?int $quantity;
    public ?int $serviceDate;
    public ?string $notes;
}
