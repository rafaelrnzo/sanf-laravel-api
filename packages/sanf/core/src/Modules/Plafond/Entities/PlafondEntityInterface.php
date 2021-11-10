<?php


namespace Sanf\Core\Modules\Plafond\Entities;


interface PlafondEntityInterface
{
    public function __construct(array $attributes);

    public function getId(): string;

    public function getCustomerId(): string;

    public function getCurrentBalance(): string;

    public function getUsedBalance(): string;

    public function getRemainingBalance(): string;

    public function getType(): PlafondTypeEntityInterface;

    public function getUpdatedAt(): \DateTimeImmutable;

    /**
     * @return PlafondHistoryEntityInterface[]
     */
    public function getHistories(): array;
}
