<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;

class CustomerProfileTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        return [
            "xid" => $dto->xid,
            "type_id" => $dto->typeId,
            "title" => $dto->title,
            "type_name" => $dto->typeName,
            "full_name" => $dto->fullName,
            "pic_name" => $dto->picName,
            "identity_number" => $dto->identityNumber,
            "npwp" => $dto->npwp,
            "email" => $dto->email,
            "landline_number" => $dto->landlineNumber,
            "phone_number" => $dto->phoneNumber,
            "gender" => $dto->gender,
            "birthdate" => optional($dto->birthdate)->format('Y-m-d'),
            "country_id" => $dto->countryId,
            "country_name" => $dto->countryName,
            "province_id" => $dto->provinceId,
            "province_name" => $dto->provinceName,
            "city_id" => $dto->cityId,
            "city_name" => $dto->cityName,
            "district_name" => $dto->districtName,
            "subdistrict_name" => $dto->subdistrictName,
            "postcode" => $dto->postcode,
            "address" => $dto->address,
            "business_since" => $dto->businessSince,
            "is_pic" => $dto->isPic
        ];
    }
}
