<?php

namespace Sanf\Core\Modules\Plafond\Entities;

use Carbon\CarbonImmutable;
use Sanf\Core\Modules\Plafond\Enums\PlafondStatusEnum;

final class GuzzlePlafondHistoryEntity implements PlafondHistoryEntityInterface
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

    public function getType(): PlafondTypeEntityInterface
    {
        return new EloquentPlafondTypeEntity($this->attributes['type']);
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

    public function getStatus(): PlafondStatusEnum
    {
        $status = $this->attributes['P_STATUS'];
        if (strlen($status) === 3) {
            $status = '1' . substr($status, 1);
        }

        return new PlafondStatusEnum($status);
    }

    public function getStatusLabel(): PlafondStatusEnum
    {
        return $this->attributes['DESCRIPTION'];
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return CarbonImmutable::parse($this->attributes['DATE_UPDATE']);
    }

    public function getNotes()
    {
        return $this->attributes['NOTES'] ?? null;
    }
}
