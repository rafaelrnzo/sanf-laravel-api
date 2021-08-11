<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;

class CustomerProfileTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        return [
            "xid" => $dto->xid,
            "type_id" => $dto->type_id,
            "type_name" => $dto->type_name,
            "full_name" => $dto->full_name,
            "pic_name" => $dto->pic_name,
            "identity_number" => $dto->identity_number,
            "npwp" => $dto->npwp,
            "email" => $dto->email,
            "landline_number" => $dto->landline_number,
            "phone_number" => $dto->phone_number,
            "gender" => $dto->gender,
            "birthdate" => $dto->birthdate,
            "country_id" => $dto->country_id,
            "country_name" => $dto->country_name,
            "province_id" => $dto->province_id,
            "province_name" => $dto->province_name,
            "city_id" => $dto->city_id,
            "city_name" => $dto->city_name,
            "district_name" => $dto->district_name,
            "subdistrict_name" => $dto->subdistrict_name,
            "postcode" => $dto->postcode,
            "address" => $dto->address,
            "business_since" => $dto->business_since,
            "is_active" => $dto->is_active,
            "is_pic" => $dto->is_pic
        ];
    }
}
