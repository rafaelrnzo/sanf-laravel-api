<?php


namespace Sanf\Core\Modules\Plafond\Entities;


use Carbon\Carbon;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;

final class PlafondEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->attributes['PLAFONDHEADER_ID'];
    }

    public function getCustomerId(): string
    {
        return $this->attributes['CUST_ID'];
    }

    public function getCurrentBalance(): string
    {
        return $this->attributes['P_CURRENT'];
    }

    public function getUsedBalance(): string
    {
        return $this->attributes['P_USED'];
    }

    public function getRemainingBalance(): string
    {
        return $this->attributes['P_SISA'];
    }

    public function getType(): PlafondTypeEnum
    {
        return new PlafondTypeEnum($this->attributes['P_CODE']);
    }

    public function getUpdatedAt(): Carbon
    {
//        return Carbon::createFromFormat('03-NOV-21');
    }

    public function getHistories(): array
    {
        return array_map(function ($item) {
            return new PlafondHistoryEntity($item);
        }, $this->attributes['items']);
    }
}
