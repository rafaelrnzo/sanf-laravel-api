<?php


namespace Sanf\Core\Modules\User\Entities;


use Carbon\CarbonImmutable;

final class RestProfileEntity implements ProfileEntityInterface
{
    private array $attributes;

    public function setAsPic()
    {
        $this->attributes['PIC'] = true;
    }

    public function setNotAsPic()
    {
        $this->attributes['PIC'] = false;
    }

    const SNAPSHOT_VERSION = 1;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getCustomerId()
    {
        return $this->attributes['CUST_ID_SANF'];
    }

    public function getTypeId()
    {
        return $this->attributes['ID_IDENTITY'];
    }

    public function getTypeName()
    {
        return $this->attributes['DESC_IDENTITY'];
    }

    public function getTitle()
    {
        return $this->attributes['COMPANY_TYPE'];
    }

    public function getFullName()
    {
        return $this->attributes['IDENTITY_NAME'];
    }

    public function getPicName()
    {
        return $this->attributes['PIC_NAME'];
    }

    public function getIdentityNumber()
    {
        return $this->attributes['KTP'];
    }

    public function getNpwp()
    {
        return $this->attributes['NPWP'];
    }

    public function getEmail()
    {
        return $this->attributes['EMAIL_ADDR'];
    }

    public function getLandlineNumber()
    {
        return $this->attributes['NO_TELP'];
    }

    public function getPhoneNumber()
    {
        return $this->attributes['NO_HP'];
    }

    public function getGender()
    {
        return $this->attributes['GENDER'];
    }

    public function getBirthdate(): ?\DateTimeImmutable
    {
        return CarbonImmutable::make($this->attributes['TGL_LAHIR']);
    }

    public function getCountryId()
    {
        return $this->attributes['ID_NEGARA'];
    }

    public function getCountryName()
    {
        return $this->attributes['NEGARA'];
    }

    public function getProvinceId()
    {
        return $this->attributes['ID_PROVINSI'];
    }

    public function getProvinceName()
    {
        return $this->attributes['PROVINSI'];
    }

    public function getCityId()
    {
        return $this->attributes['ID_KOTA'];
    }

    public function getCityName()
    {
        return $this->attributes['KOTA'];
    }

    public function getDistrictName()
    {
        return $this->attributes['KECAMATAN'];
    }

    public function getSubdistrictName()
    {
        return $this->attributes['NEGARA'];
    }

    public function getPostcode()
    {
        return $this->attributes['KODEPOS'];
    }

    public function getAddress()
    {
        return $this->attributes['ALAMAT'];
    }

    public function getBusinessSince()
    {
        return $this->attributes['LAMA_USAHA'];
    }

    public function getIsPic()
    {
        return (bool)$this->attributes['PIC'];
    }

    public function getPersonalAssistantContactNumber()
    {
        return $this->attributes['NO_AE'];
    }

    public function toArray()
    {
        return [
            'xid' => $this->getCustomerId(),
            'customer_id' => $this->getCustomerId(),
            'type_id' => $this->getTypeId(),
            'type_name' => $this->getTypeName(),
            'title' => $this->getTitle(),
            'full_name' => $this->getFullName(),
            'pic_name' => $this->getPicName(),
            'identity_number' => $this->getIdentityNumber(),
            'npwp' => $this->getNpwp(),
            'email' => $this->getEmail(),
            'landline_number' => $this->getLandlineNumber(),
            'phone_number' => $this->getPhoneNumber(),
            'gender' => $this->getGender(),
            'birthdate' => $this->getBirthdate(),
            'country_id' => $this->getCountryId(),
            'country_name' => $this->getCountryName(),
            'province_id' => $this->getProvinceId(),
            'province_name' => $this->getProvinceName(),
            'city_id' => $this->getCityId(),
            'city_name' => $this->getCityName(),
            'district_name' => $this->getDistrictName(),
            'subdistrict_name' => $this->getSubdistrictName(),
            'postcode' => $this->getPostcode(),
            'address' => $this->getAddress(),
            'business_since' => $this->getBusinessSince(),
            'is_pic' => $this->getIsPic(),
            'personal_assistant_contact_number' => $this->getIsPic(),
        ];
    }

    public function getSnapshot()
    {
        return [
            'xid' => $this->getCustomerId(),
            'customer_id' => $this->getCustomerId(),
            'type_id' => $this->getTypeId(),
            'type_name' => $this->getTypeName(),
            'title' => $this->getTitle(),
            'full_name' => $this->getFullName(),
            'pic_name' => $this->getPicName(),
            'identity_number' => $this->getIdentityNumber(),
            'npwp' => $this->getNpwp(),
            'email' => $this->getEmail(),
            'landline_number' => $this->getLandlineNumber(),
            'phone_number' => $this->getPhoneNumber(),
            'gender' => $this->getGender(),
            'birthdate' => $this->getBirthdate(),
            'country_id' => $this->getCountryId(),
            'country_name' => $this->getCountryName(),
            'province_id' => $this->getProvinceId(),
            'province_name' => $this->getProvinceName(),
            'city_id' => $this->getCityId(),
            'city_name' => $this->getCityName(),
            'district_name' => $this->getDistrictName(),
            'subdistrict_name' => $this->getSubdistrictName(),
            'postcode' => $this->getPostcode(),
            'address' => $this->getAddress(),
            'business_since' => $this->getBusinessSince(),
            'is_pic' => $this->getIsPic(),
            'personal_assistant_contact_number' => $this->getIsPic(),
            'version' => self::SNAPSHOT_VERSION
        ];
    }
}
