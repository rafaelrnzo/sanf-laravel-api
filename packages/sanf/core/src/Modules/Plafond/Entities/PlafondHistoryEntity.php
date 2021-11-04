<?php


namespace Sanf\Core\Modules\Plafond\Entities;


use Carbon\Carbon;
use Sanf\Core\Modules\Plafond\Enums\PlafondStatusEnum;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;

final class PlafondHistoryEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getPlafondId(): string
    {
        return $this->attributes['PLAFONDHEADER_ID'];
    }

    public function getCustomerId(): string
    {
        return $this->attributes['CUST_ID'];
    }

    public function getSubmittedBalance(): string
    {
        return $this->attributes['P_SUBMIT'];
    }

    public function getCurrentBalance(): string
    {
        return $this->attributes['P_CURRENT'];
    }

    public function getAddedBalance(): string
    {
        return $this->attributes['P_TAMBAHAN'];
    }

    public function getType(): PlafondTypeEnum
    {
        return $this->attributes['P_CODE'];
    }

    public function getStatus(): PlafondStatusEnum
    {
        return new PlafondStatusEnum($this->attributes['P_STATUS']);
    }

    public function getUpdatedAt(): Carbon
    {
//        return Carbon::createFromFormat('03-NOV-21');
    }
}
