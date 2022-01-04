<?php

namespace Sanf\Core\Modules\Survey\Entities;

final class SurveyEntity implements SurveyEntitiesInterface
{
    protected array $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getXid(): string
    {
        return $this->attributes['xid'];
    }

    public function getBranchId(): int
    {
        return $this->attributes['branch_id'];
    }

    public function getContractNo(): string
    {
        return $this->attributes['contract_no'];
    }

    public function getProfileXid()
    {
        return $this->attributes['profile_xid'];
    }

    public function getCustomerName()
    {
        return $this->attributes['customer_name'];
    }

    public function getProjectName()
    {
        return $this->attributes['project_name'];
    }

    public function getProjetLocation()
    {
        return $this->attributes['project_location'];
    }
}
