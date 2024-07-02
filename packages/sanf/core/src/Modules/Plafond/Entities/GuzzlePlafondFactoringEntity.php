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
        return $this->attributes['P_TOTAL'] ?? '0';
    }

    public function getUsedBalance(): string
    {
        return $this->attributes['P_TERPAKAI'] ?? '0';
    }

    public function getRemainingBalance(): string
    {
        return $this->attributes['P_SISA'] ?? '0';
    }

    public function getAddedBalance(): string
    {
        return $this->attributes['P_TAMBAHAN'] ?? '0';
    }

    public function getCustomerReview(): string
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
