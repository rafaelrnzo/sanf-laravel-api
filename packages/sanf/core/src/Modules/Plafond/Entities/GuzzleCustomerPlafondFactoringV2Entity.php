<?php

namespace Sanf\Core\Modules\Plafond\Entities;

class GuzzleCustomerPlafondFactoringV2Entity
{
    private string $id;
    private string $name;
    private string $email;
    private string $code;

    public function __construct(array $data)
    {
        $this->id = $data['ID'] ?? '';
        $this->name = $data['NAME'] ?? '';
        $this->email = $data['EMAIL'] ?? '';
        $this->code = $data['CODE'] ?? '';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}