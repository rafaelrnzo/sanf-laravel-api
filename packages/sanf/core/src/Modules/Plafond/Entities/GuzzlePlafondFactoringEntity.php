<?php

namespace Sanf\Core\Modules\Plafond\Entities;

use Carbon\CarbonImmutable;

final class GuzzlePlafondFactoringEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getPlafondId(): string
    {
        return $this->attributes['NO_PLAFOND'];
    }

    public function getCustomerId(): string
    {
        return $this->attributes['CUST_ID'];
    }

    public function getCurrentBalance(): string
    {
        return $this->attributes['P_TOTAL'] ?? '';
    }

    public function getUsedBalance(): string
    {
        return $this->attributes['P_TERPAKAI'] ?? '';
    }

    public function getRemainingBalance(): string
    {
        return $this->attributes['P_SISA'] ?? '';
    }

    public function getAddedBalance(): string
    {
        return $this->attributes['P_TAMBAHAN'] ?? '';
    }

    public function getCustomerReview(): bool
    {
        return $this->attributes['CUSTOMER_REVIEW'];
    }

    public function getCustomers(): array
    {
        return array_map(function ($item) {
            return new GuzzleCustomerPlafondFactoringEntity($item);
        }, $this->attributes['CUSTOMER']);
    }

    public function getExpiredAt(): \DateTimeImmutable
    {
        return CarbonImmutable::parse($this->attributes['EXP_DATE']);
    }
}
