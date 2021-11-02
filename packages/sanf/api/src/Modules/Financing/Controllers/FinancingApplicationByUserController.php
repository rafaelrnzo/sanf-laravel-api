<?php


namespace Sanf\Api\Modules\Financing\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Financing\Transformers\FinancingApplicationSimpleTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingApplicationTransformer;
use Sanf\Core\Modules\Financing\Dto\AddFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Dto\BrowseFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Dto\FinancingObjectDto;
use Sanf\Core\Modules\Financing\Dto\ReadFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Services\AddCompanyFinancingApplicationByUserService;
use Sanf\Core\Modules\Financing\Services\AddPersonalFinancingApplicationByUserService;
use Sanf\Core\Modules\Financing\Services\BrowseFinancingApplicationByUserService;
use Sanf\Core\Modules\Financing\Services\ReadFinancingApplicationByUserService;
use Sanf\Core\Modules\User\Services\GetDetailCustomerProfileService;

class FinancingApplicationByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseFinancingApplicationByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowseFinancingApplicationDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result, new FinancingApplicationSimpleTransformer());
    }

    public function getRead(Guard $auth, $xid, ReadFinancingApplicationByUserService $service)
    {
        $dto = new ReadFinancingApplicationDto([
            'xid' => $xid,
            'userId' => $auth->id()
        ]);
        $result = $service->execute($dto);
        return fractal($result, new FinancingApplicationTransformer());
    }

    public function postAddByPersonalProfile(
        Guard $auth,
        Request $request,
        AddPersonalFinancingApplicationByUserService $financingService,
        GetDetailCustomerProfileService $profileService
    ) {
        $input = $this->validate($request, [
            'profile_xid' => ['required', 'string'],
            'financing_facility_id' => ['required', 'integer'],
            'financing_method_id' => ['required', 'integer'],
            'financing_objects' => ['nullable', 'array'],
            'financing_objects.*.amount' => ['required_with:financing_objects', 'integer'],
            'financing_objects.*.provider_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.brand_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.brand_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.type_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.type_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.model_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.model_name' => ['required_with:financing_objects', 'string'],
            'is_receive_offer' => ['required', 'boolean'],
        ]);

        $profile = $profileService->execute((object)[
            'userId' => $auth->id(),
            'customerId' => $input['profile_xid']
        ]);
        $financingObjects = [];
        foreach ($input['financing_objects'] ?? [] as $financingObject) {
            $financingObjects[] = new FinancingObjectDto($financingObject);
        }
        $dto = new AddFinancingApplicationDto($input + [
                'userId' => $auth->id(),
                'profile' => $profile,
                'financingObjects' => $financingObjects
            ]);
        $financingService->execute($dto);
        return $this->responseOk();
    }

    public function postAddByCompanyProfile(
        Guard $auth,
        Request $request,
        AddCompanyFinancingApplicationByUserService $financingService,
        GetDetailCustomerProfileService $profileService
    ) {
        $input = $this->validate($request, [
            'profile_xid' => ['required', 'string'],
            'financing_facility_id' => ['required', 'integer'],
            'financing_method_id' => ['required', 'integer'],
            'financing_objects' => ['nullable', 'array'],
            'financing_objects.*.amount' => ['required_with:financing_objects', 'integer'],
            'financing_objects.*.provider_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.brand_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.brand_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.type_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.type_name' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.model_id' => ['required_with:financing_objects', 'string'],
            'financing_objects.*.model_name' => ['required_with:financing_objects', 'string'],
            'is_receive_offer' => ['required', 'boolean'],
        ]);
        $profile = $profileService->execute((object)[
            'userId' => $auth->id(),
            'customerId' => $input['profile_xid']
        ]);
        $dto = new AddFinancingApplicationDto($input + [
                'userId' => $auth->id(),
                'profile' => $profile,
            ]);
        $financingService->execute($dto);
        return $this->responseOk();
    }
}
