<?php

namespace Sanf\Core\Modules\Bank\Entities;

use Carbon\CarbonImmutable;

class GuzzleUserBankAccountEntity
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getBankId(): string
    {
        return $this->attributes['ID'];
    }

    public function getOwner(): string
    {
        return $this->attributes['OWNER'];
    }

    public function getProvider(): string
    {
        return $this->attributes['PROVIDER'];
    }

    public function getAccountNo(): string
    {
        return $this->attributes['ACCOUNT_NUMBER'];
    }

    public function getIsDefault(): bool
    {
        return $this->attributes['IS_DEFAULT'];
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return CarbonImmutable::parse($this->attributes['DATE_UPDATE']);
    }
}
