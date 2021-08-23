<?php


namespace Sanf\Api\Modules\User\Transformers;


use League\Fractal\TransformerAbstract;
use function optional;

class CustomerProfileTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        return [
            "xid" => $dto->xid,
            "type_id" => $dto->typeId,
            "title" => empty(trim($dto->title)) ? null : $dto->title,
            "type_name" => $dto->typeName,
            "full_name" => $dto->fullName,
            "pic_name" => $dto->picName,
            "identity_number" => empty(trim($dto->identityNumber)) ? null : $dto->identityNumber,
            "npwp" => empty(trim($dto->npwp)) ? null : $dto->npwp,
            "email" => $dto->email,
            "landline_number" => $dto->landlineNumber,
            "phone_number" => $dto->phoneNumber,
            "gender" => empty(trim($dto->gender)) ? null : $dto->gender,
            "birthdate" => optional($dto->birthdate)->format('Y-m-d'),
            "country" => empty(trim($dto->countryId)) ? null : [
                "country_id" => $dto->countryId,
                "country_name" => $dto->countryName,
            ],
            "province" => empty(trim($dto->provinceId)) ? null : [
                "country_id" => $dto->countryId,
                "country_name" => $dto->countryName,
                "province_id" => $dto->provinceId,
                "province_name" => $dto->provinceName,
            ],
            "city" => empty(trim($dto->cityId)) ? null : [
                "country_id" => $dto->countryId,
                "province_id" => $dto->provinceId,
                "city_id" => $dto->cityId,
                "city_name" => $dto->cityName,
            ],
            "district" => empty(trim($dto->districtName)) ? null : [
                "country_id" => $dto->countryId,
                "province_id" => $dto->provinceId,
                "city_id" => $dto->cityId,
                "district_name" => $dto->districtName
            ],
            "subdistrict" => empty(trim($dto->subdistrictName)) ? null : [
                "country_id" => $dto->countryId,
                "province_id" => $dto->provinceId,
                "city_id" => $dto->cityId,
                "district_name" => $dto->districtName,
                "subdistrict_name" => $dto->subdistrictName,
                "postcode" => $dto->postcode,
            ],
            "address" => empty(trim($dto->address)) ? null : $dto->address,
            "business_since" => $dto->businessSince,
            "is_pic" => $dto->isPic
        ];
    }
}
