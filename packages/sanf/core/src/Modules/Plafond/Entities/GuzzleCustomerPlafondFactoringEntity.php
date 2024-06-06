<?php

namespace Sanf\Core\Modules\Plafond\Entities;

final class GuzzleCustomerPlafondFactoringEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->attributes['ID'] ?? '';
    }

    public function getName(): string
    {
        return $this->attributes['NAME'] ?? '';
    }

    public function getCode(): string
    {
        return $this->attributes['CODE'] ?? '';
    }

    public function getEmail(): string
    {
        return $this->attributes['EMAIL'] ?? '';
    }

    public function getCustomerId(): string
    {
        return $this->attributes['CUST_ID'] ?? '';
    }
}
