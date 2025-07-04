<?php

namespace Sanf\Core\Modules\Plafond\Entities;

class GuzzlePlafondFactoringV2Entity
{
    private string $plafondNo;
    private string $custId;
    private string $plafondCode;
    private string $totalAmount;
    private string $usedAmount;
    private string $remainingAmount;
    private string $expiredDate;
    private string $pksNo;
    private string $pksDate;
    private string $customerReview;
    private GuzzleCustomerPlafondFactoringV2Entity $customer;
    private array $virtualAccounts;

    public function __construct(array $data)
    {
        $this->plafondNo = $data['NO_PLAFOND'] ?? '';
        $this->custId = $data['CUST_ID'] ?? '';
        $this->plafondCode = $data['P_CODE'] ?? '';
        $this->totalAmount = $data['P_TOTAL'] ?? '0';
        $this->usedAmount = $data['P_TERPAKAI'] ?? '0';
        $this->remainingAmount = $data['P_SISA'] ?? '0';
        $this->expiredDate = $data['EXP_DATE'] ?? '';
        $this->pksNo = $data['PKS_NO'] ?? '';
        $this->pksDate = $data['PKS_DATE'] ?? '';
        $this->customerReview = $data['CUSTOMER_REVIEW'] ?? 'N';
        $this->customer = new GuzzleCustomerPlafondFactoringV2Entity($data['CUSTOMER'] ?? []);
        $this->virtualAccounts = array_map(function ($va) {
            return new GuzzleVirtualAccountEntity($va);
        }, $data['VIRTUAL_ACCOUNT'] ?? []);
    }

    public function getPlafondNo(): string
    {
        return $this->plafondNo;
    }

    public function getCustId(): string
    {
        return $this->custId;
    }

    public function getPlafondCode(): string
    {
        return $this->plafondCode;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function getUsedAmount(): string
    {
        return $this->usedAmount;
    }

    public function getRemainingAmount(): string
    {
        return $this->remainingAmount;
    }

    public function getExpiredDate(): string
    {
        return $this->expiredDate;
    }

    public function getPksNo(): string
    {
        return $this->pksNo;
    }

    public function getPksDate(): string
    {
        return $this->pksDate;
    }

    public function getCustomerReview(): string
    {
        return $this->customerReview;
    }

    public function getCustomer(): GuzzleCustomerPlafondFactoringV2Entity
    {
        return $this->customer;
    }

    public function getVirtualAccounts(): array
    {
        return $this->virtualAccounts;
    }
}
