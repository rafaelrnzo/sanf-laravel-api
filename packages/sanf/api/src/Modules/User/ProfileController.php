<?php


namespace Sanf\Api\Modules\User;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\User\GetDetailCustomerProfileService;
use Sanf\Core\Modules\User\GetListCustomerProfileService;
use Sanf\Core\Modules\User\GetMyProfileService;
use Sanf\Core\Modules\User\RegisterAsContractOwnerService;
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

    public function postCreateCompanyProfile(Request $request)
    {
        $this->validate($request, [
            "title" => ["required", "string"],
            "full_name" => ["required", "string"],
            "npwp" => ["required", "string"],
            "landline_number" => ["nullable", "string"],
            "pic_name" => ["required", "string"],
            "phone_number" => ["required", "string"],
            "email" => ["required", "string"]
        ]);
        return $this->responseOk();
    }

    public function putUpdateCompanyProfile(Request $request)
    {
        $this->validate($request, [
            "email" => ["required", "string"],
        ]);
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
