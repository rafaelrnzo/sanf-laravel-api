<?php


namespace Sanf\Core\Modules\User\Entities;


interface ProfileEntityInterface
{
    public function getCustomerId();

    public function getTypeId();

    public function getTypeName();

    public function getTitle();

    public function getFullName();

    public function getPicName();

    public function getIdentityNumber();

    public function getNpwp();

    public function getEmail();

    public function getLandlineNumber();

    public function getPhoneNumber();

    public function getGender();

    public function getBirthdate(): \DateTimeImmutable;

    public function getCountryId();

    public function getCountryName();

    public function getProvinceId();

    public function getProvinceName();

    public function getCityId();

    public function getCityName();

    public function getDistrictName();

    public function getSubdistrictName();

    public function getPostcode();

    public function getAddress();

    public function getBusinessSince();

    public function getIsPic();

    public function getPersonalAssistantContactNumber();

    public function setAsPic();

    public function setNotAsPic();

    public function toArray();

    public function getSnapshot();
}
