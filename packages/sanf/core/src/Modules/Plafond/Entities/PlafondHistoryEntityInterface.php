<?php

namespace Sanf\Core\Modules\Plafond\Entities;

use Sanf\Core\Modules\Plafond\Enums\PlafondStatusEnum;

interface PlafondHistoryEntityInterface
{
    public function __construct(array $attributes);

    public function getPlafondId(): string;

    public function getType(): PlafondTypeEntityInterface;

    public function getCustomerId(): string;

    public function getCurrentBalance(): string;

    public function getUsedBalance(): string;

    public function getRemainingBalance(): string;

    public function getAddedBalance(): string;

    public function getStatus(): PlafondStatusEnum;

    public function getUpdatedAt(): \DateTimeImmutable;
}
