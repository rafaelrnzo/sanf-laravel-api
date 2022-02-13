<?php


namespace Sanf\Api\Modules\User\Controllers;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\CustomerProfileSimpleTransformer;
use Sanf\Api\Modules\User\Transformers\CustomerProfileTransformer;
use Sanf\Api\Modules\User\Transformers\UserMetadataAccountReceivableTransformer;
use Sanf\Api\Modules\User\Transformers\UserMetadataContractTransformer;
use Sanf\Core\Modules\User\Services\CreateCompanyProfileService;
use Sanf\Core\Modules\User\Services\GetListEligibleCustomerProfileService;
use Sanf\Core\Modules\User\Services\GetMyProfileService;
use Sanf\Core\Modules\User\Services\GetUserMetadataAccountReceivableService;
use Sanf\Core\Modules\User\Services\GetUserMetadataContractService;
use Sanf\Core\Modules\User\Services\RegisterAsContractOwnerService;
use Sanf\Core\Modules\User\Services\SwitchActiveCustomerProfileService;
use Sanf\Core\Modules\User\Services\UpdateCompanyProfileService;
use Sanf\Core\Modules\User\Services\UpdatePersonalProfileService;
use Spatie\Fractalistic\ArraySerializer;

class ProfileController extends RestApiController
{
    public function getList(Guard $auth, GetListEligibleCustomerProfileService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'email' => $auth->user()->username
        ];
        $result = $service->execute($dto);
        return fractal($result, CustomerProfileSimpleTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function getDetail(Guard $auth, $xid, \Sanf\Core\Modules\User\Services\GetDetailCustomerProfileByUserService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'customerId' => $xid
        ];
        $result = $service->execute($dto);
        return fractal($result, CustomerProfileTransformer::class);
    }

    public function postCreateCompanyProfile(Guard $auth, $xid, Request $request, CreateCompanyProfileService $service)
    {
        $input = $this->validate($request, [
            "title" => ["required", "string"],
            "full_name" => ["required", "string"],
            "npwp" => ["required", "string"],
            "landline_number" => ["nullable", "string"],
            "pic_name" => ["required", "string"],
            "phone_number" => ["nullable", "string"],
            "email" => ["required", "string"]
        ]);
        $dto = (object)[
            "userId" => $auth->id(),
            "customerId" => $xid,
            "title" => $input['title'],
            "fullName" => $input['full_name'],
            "npwp" => $input['npwp'],
            "landlineNumber" => $input['landline_number'] ?? null,
            "picName" => $input['pic_name'],
            "phoneNumber" => $input['phone_number'] ?? null,
            "email" => $input['email']
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function putUpdateCompanyProfile(Guard $auth, Request $request, $xid, UpdateCompanyProfileService $service)
    {
        $input = $this->validate($request, [
            "landline_number" => ["nullable", "string"],
            "phone_number" => ["required", "string"],
            "province_id" => ["required", "string"],
            "province_name" => ["required", "string"],
            "city_id" => ["required", "string"],
            "city_name" => ["required", "string"],
            "district_name" => ["required", "string"],
            "subdistrict_name" => ["required", "string"],
            "postcode" => ["required", "string"],
            "address" => ["required", "string"],
            "business_since" => ["nullable", "string"],
        ]);
        $dto = (object)[
            "userId" => $auth->id(),
            "customerId" => $xid,
            "landlineNumber" => $input['landline_number'] ?? null,
            "phoneNumber" => $input['phone_number'],
            "provinceId" => $input['province_id'],
            "provinceName" => $input['province_name'],
            "cityId" => $input['city_id'],
            "cityName" => $input['city_name'],
            "districtName" => $input['district_name'],
            "subdistrictName" => $input['subdistrict_name'],
            "postcode" => $input['postcode'],
            "address" => $input['address'],
            "businessSince" => $input['business_since'] ?? null,
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function putUpdatePersonalProfile(Guard $auth, Request $request, $xid, UpdatePersonalProfileService $service)
    {
        $input = $this->validate($request, [
            "identity_number" => ["required", "string"],
            "landline_number" => ["nullable", "string"],
            "phone_number" => ["required", "string"],
            "gender" => ["required", "in:F,M"], //TODO ENUM
            "birthdate" => ["required", "date_format:Y-m-d"],
            "province_id" => ["required", "string"],
            "province_name" => ["required", "string"],
            "city_id" => ["required", "string"],
            "city_name" => ["required", "string"],
            "district_name" => ["required", "string"],
            "subdistrict_name" => ["required", "string"],
            "postcode" => ["required", "string"],
            "address" => ["required", "string"],
            "business_since" => ["nullable", "string"],
        ]);
        $dto = (object)[
            "userId" => $auth->id(),
            "customerId" => $xid,
            "identityNumber" => $input['identity_number'],
            "landlineNumber" => $input['landline_number'] ?? null,
            "phoneNumber" => $input['phone_number'],
            "birthdate" => $input['birthdate'],
            "gender" => $input['gender'],
            "provinceId" => $input['province_id'],
            "provinceName" => $input['province_name'],
            "cityId" => $input['city_id'],
            "cityName" => $input['city_name'],
            "districtName" => $input['district_name'],
            "subdistrictName" => $input['subdistrict_name'],
            "postcode" => $input['postcode'],
            "address" => $input['address'],
            "businessSince" => $input['business_since'] ?? null,
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postRegisterWithContract(Request $request, RegisterAsContractOwnerService $service)
    {
        $validated = $this->validate($request, [
            "email" => ["required", "string"],
        ]);
        $dto = (object)[
            'email' => $validated['email']
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    /**
     * @param Guard $guard
     * @param $xid
     * @param SwitchActiveCustomerProfileService $service
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     * @deprecated Switch Profile Not Persisted On Backend Anymore
     */
    public function postSwitch(Guard $guard, $xid, SwitchActiveCustomerProfileService $service)
    {
        $dto = (object)[
            'userId' => $guard->id(),
            'customerId' => $xid
        ];
        $service->execute($dto);
        $this->responseOk();
    }

    public function getMyProfile(Guard $auth, GetMyProfileService $service)
    {
        $dto = (object)['userId' => $auth->id()];

        $data = $service->execute($dto);

        return $this->responseOk('Success', fractal($data, config('auth.transformers.profile')));
    }

    public function getMetadataContract(Guard $auth, $xid, GetUserMetadataContractService $service)
    {
        $dto = (object)['user_id' => $auth->id(), 'profile_xid' => $xid];
        $result = $service->execute($dto);

        return fractal($result, UserMetadataContractTransformer::class);
    }

    public function getMetadataAccountReceivable(
        Guard $auth,
        $xid,
        Request $request,
        GetUserMetadataAccountReceivableService $service
    ) {
        $input = $this->validate($request, [
            'currency_type' => ['nullable', 'in:IDR,USD']
        ]);

        $dto = (object)[
            'user_id' => $auth->id(),
            'profile_xid' => $xid,
            'currency_type' => $input['currency_type'] ?? 'IDR',
        ];
        $result = $service->execute($dto);

        return fractal($result, UserMetadataAccountReceivableTransformer::class);
    }
}
