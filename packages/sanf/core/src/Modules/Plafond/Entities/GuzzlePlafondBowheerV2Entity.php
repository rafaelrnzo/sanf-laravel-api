<?php

namespace Sanf\Core\Modules\Plafond\Entities;

class GuzzlePlafondBowheerV2Entity
{
    private string $customerId;
    private string $bowheerId;
    private ?string $bowheerName;
    private ?string $bowheerEmail;
    private ?string $bowheerEmailAlias;
    private ?string $bowheerEmailCc;
    private string $bowheerCode;
    private string $bowheerAddress;

    public function __construct(array $data)
    {
        $this->customerId = $data['CUST_ID'] ?? '';
        $this->bowheerId = $data['BOWHEER_ID'] ?? '';
        $this->bowheerName = $data['BOWHEER_NAME'] ?? null;
        $this->bowheerEmail = $data['BOWHEER_EMAIL'] ?? null;
        $this->bowheerEmailAlias = $data['BOWHEER_EMAIL_ALIAS'] ?? null;
        $this->bowheerEmailCc = $data['BOWHEER_EMAIL_CC'] ?? null;
        $this->bowheerCode = $data['BOWHEER_CODE'] ?? '';
        $this->bowheerAddress = $data['BOWHEER_ADDRESS'] ?? '';
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getBowheerId(): string
    {
        return $this->bowheerId;
    }

    public function getBowheerName(): ?string
    {
        return $this->bowheerName;
    }

    public function getBowheerEmail(): ?string
    {
        return $this->bowheerEmail;
    }

    public function getBowheerNameForEmail(): ?string
    {
        return $this->bowheerEmailAlias ?? $this->bowheerName;
    }

    public function getBowheerEmailCc(): ?array
    {
        if (empty($this->bowheerEmailCc)) {
            return null;
        }

        return array_map('trim', explode(',', $this->bowheerEmailCc));
    }

    public function getBowheerCode(): string
    {
        return $this->bowheerCode;
    }

    public function getBowheerAddress(): string
    {
        return $this->bowheerAddress;
    }
}
