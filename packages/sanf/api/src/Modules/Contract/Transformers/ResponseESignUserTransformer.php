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
            'postal_code' => $item->postalCode,
            'province' => $item->province,
            'city' => $item->city,
            'district' => $item->district,
            'sub_district' => $item->subDistrict,
            'selfie_url' => ($item->selfieFile) ? file_get_temp_url($item->selfieFile->path) : null,
            'identity_url' => ($item->identityFile) ? file_get_temp_url($item->identityFile->path) : null,
            'status' => $item->statusId,
        ];
    }
}
