<?php


namespace Sanf\Api\Modules\User;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\User\GetDetailCustomerProfileService;
use Sanf\Core\Modules\User\GetListCustomerProfileService;
use Sanf\Core\Modules\User\GetMyProfileService;
use Sanf\Core\Modules\User\RegisterAsContractOwnerService;
use Sanf\Core\Modules\User\Services\CreateCompanyProfileService;
use Sanf\Core\Modules\User\Services\UpdateCompanyProfileService;
use Sanf\Core\Modules\User\SwitchActiveCustomerProfileService;

class ProfileController extends RestController
{
    public function getList(Guard $auth, GetListCustomerProfileService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'email' => $auth->user()->username
        ];
        $result = $service->execute($dto);
        return fractal($result, CustomerProfileSimpleTransformer::class);
    }

    public function getDetail(Guard $auth, $xid, GetDetailCustomerProfileService $service)
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
            "phone_number" => ["required", "string"],
            "email" => ["required", "string"]
        ]);
        $dto = (object)[
            "userId" => $auth->id(),
            "customerId" => $xid,
            "title" => $input['title'],
            "fullName" => $input['full_name'],
            "npwp" => $input['npwp'],
            "landlineNumber" => $input['landline_number'],
            "picName" => $input['pic_name'],
            "phoneNumber" => $input['phone_number'],
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
            "business_since" => ["required", "string"],
        ]);
        $dto = (object)[
            "userId" => $auth->id(),
            "customerId" => $xid,
            "landlineNumber" => $input['landline_number'],
            "phoneNumber" => $input['phone_number'],
            "provinceId" => $input['province_id'],
            "provinceName" => $input['province_name'],
            "cityId" => $input['city_id'],
            "cityName" => $input['city_name'],
            "districtName" => $input['district_name'],
            "subdistrictName" => $input['subdistrict_name'],
            "postcode" => $input['postcode'],
            "address" => $input['address'],
            "businessSince" => $input['business_since'],
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function putUpdatePersonalProfile(Request $request)
    {
        $this->validate($request, [
            "email" => ["required", "string"],
        ]);
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
}
