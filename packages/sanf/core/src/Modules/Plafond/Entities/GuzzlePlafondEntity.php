<?php


namespace Sanf\Core\Modules\Plafond\Entities;


use Carbon\CarbonImmutable;

final class GuzzlePlafondEntity implements PlafondEntityInterface
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

    public function getType(): PlafondTypeEntityInterface
    {
        return new EloquentPlafondTypeEntity($this->attributes['type']);
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return CarbonImmutable::parse($this->attributes['DATE_UPDATE']);
    }

    /**
     * @return GuzzlePlafondHistoryEntity[]
     */
    public function getHistories(): array
    {
        return array_map(function ($item) {
            return new GuzzlePlafondHistoryEntity($item);
        }, $this->attributes['items']);
    }
}
