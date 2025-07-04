<?php

namespace Sanf\Core\Modules\Plafond\Entities;

class GuzzleVirtualAccountEntity
{
    private string $custId;
    private string $currencyId;
    private string $description;
    private string $vaId;
    private string $accountName;

    public function __construct(array $data)
    {
        $this->custId = $data['CUST_ID'] ?? '';
        $this->currencyId = $data['CURR_ID'] ?? '';
        $this->description = $data['DESCRIPTION'] ?? '';
        $this->vaId = $data['VA_ID'] ?? '';
        $this->accountName = $data['ACCOUNT_NAME'] ?? '';
    }

    public function getCustId(): string
    {
        return $this->custId;
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVaId(): string
    {
        return $this->vaId;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }
}
