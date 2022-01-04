<?php

namespace Sanf\Core\Modules\Survey\Entities;

interface SurveyEntitiesInterface
{
    public function getId();
    public function getXid();
    public function getBranchId();
    public function getContractNo();
    public function getProfileXid();
    public function getCustomerName();
    public function getProjectName();
    public function getProjetLocation();
}
