<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ResponseESignUserTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'registration_id' => $item->registrationId,
            'email' => $item->email,
            'msisdn' => $item->msisdn,
            'nik' => $item->nik,
            'full_name' => Str::title($item->fullName),
            'dob' => ($item->dob) ? Carbon::createFromFormat('Y-m-d', $item->dob)->format('Y-m-d') : '',
            'pob' => Str::title($item->pob),
            'gender' => $item->gender,
            'address' => $item->address,
            'country' => empty(trim($item->countryId)) ? null : [
                'country_id' => $item->countryId,
                'country_name' => $item->countryName,
            ],
            'province' => [
                'country_id' => $item->countryId,
                'country_name' => $item->countryName,
                'province_id' => $item->provinceId,
                'province_name' => $item->provinceName,
            ],
            'city' => [
                'country_id' => $item->countryId,
                'province_id' => $item->provinceId,
                'city_id' => $item->cityId,
                'city_name' => $item->cityName,
            ],
            'district' => empty(trim($item->districtName)) ? null : [
                'country_id' => $item->countryId,
                'province_id' => $item->provinceId,
                'city_id' => $item->cityId,
                'district_name' => $item->districtName,
            ],
            'subdistrict' => empty(trim($item->subdistrictName)) ? null : [
                'country_id' => $item->countryId,
                'province_id' => $item->provinceId,
                'city_id' => $item->cityId,
                'district_name' => $item->districtName,
                'subdistrict_name' => $item->subdistrictName,
                'postcode' => $item->postcode,
            ],
            'selfie_url' => ($item->selfieFile) ? file_get_temp_url($item->selfieFile->path) : null,
            'identity_url' => ($item->identityFile) ? file_get_temp_url($item->identityFile->path) : null,
            'status' => $item->statusId,
            'is_account_expired' => $item->isAccountExpired,
        ];
    }
}
