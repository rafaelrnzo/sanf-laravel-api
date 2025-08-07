<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Insurance\Transformers\MyInsuranceClaimSubmissionSimpleTransformer;
use Sanf\Api\Modules\Insurance\Transformers\MyInsuranceClaimSubmissionTransformer;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserV2RequestDto;
use Sanf\Core\Modules\Insurance\Dtos\BrowseInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Enums\InsuranceClaimSubmissionStatusEnum;
use Sanf\Core\Modules\Insurance\Services\AddInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\AddInsuranceClaimSubmissionByUserV2Service;
use Sanf\Core\Modules\Insurance\Services\BrowseInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\ReadInsuranceClaimSubmissionByUserService;

final class InsuranceClaimSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseInsuranceClaimSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'status_id' => ['nullable', 'integer', Rule::in(InsuranceClaimSubmissionStatusEnum::ALL_STATUS)],
            'timestamp' => ['nullable', 'integer'],
        ]);
        $dto = new BrowseInsuranceClaimSubmissionByUserRequestDto($input + [
                'userId' => $auth->id(),
                'profileXid' => $xid,
            ]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyInsuranceClaimSubmissionSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getRead(Guard $auth, $xid, $submissionXid, ReadInsuranceClaimSubmissionByUserService $service)
    {
        $dto = new ReadInsuranceClaimSubmissionByUserRequestDto([
            'profileXid' => $xid,
            'xid' => $submissionXid,
            'userId' => $auth->id(),
        ]);
        $result = $service->execute($dto);

        return fractal($result, new MyInsuranceClaimSubmissionTransformer());
    }

    /**
     * @deprecated CR2025
     * @see self::postAddV2()
     */
    public function postAdd(Guard $auth, Request $request, $xid, AddInsuranceClaimSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'financing_unit' => ['required', 'array'],
            'financing_unit.serial_no' => ['required', 'string', 'max:255'],
            'financing_unit.polis_no' => ['required', 'string', 'max:255'],
            'financing_unit.brand_type_model' => ['required', 'string', 'max:255'],
            'financing_unit.year' => ['required', 'string', 'date_format:Y'],
            'location_metadata.city_id' => ['required', 'string', 'max:255'],
            'location_metadata.city_name' => ['required', 'string', 'max:255'],
            'image_files' => ['nullable', 'array'],
            'description' => ['required', 'string', 'max:65535'],
            'contract_no' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'string', 'date_format:Y-m-d'],
        ]);
        $input['incident_date'] = CarbonImmutable::make($input['incident_date']);
        $dto = new AddInsuranceClaimSubmissionByUserRequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id(),
            ]);
        $service->execute($dto);

        return $this->responseOk();
    }

    /**
     * @since CR2025
     */
    public function postAddV2(Guard $auth, Request $request, $xid, AddInsuranceClaimSubmissionByUserV2Service $service)
    {
        $input = $this->validate($request, [
            'financing_unit' => ['required', 'array'],
            'financing_unit.serial_no' => ['required', 'string', 'max:255'],
            'financing_unit.polis_no' => ['required', 'string', 'max:255'],
            'financing_unit.brand_type_model' => ['required', 'string', 'max:255'],
            'financing_unit.year' => ['required', 'string', 'date_format:Y'],
            'financing_unit.city_id' => ['nullable', 'string', 'max:255'],
            'financing_unit.city_name' => ['nullable', 'string', 'max:255'],
            'financing_unit.email_provider' => ['required', 'string', 'max:255'],
            'financing_unit.email_cc' => ['nullable', 'string', 'max:1024'],
            'location_metadata.city_id' => ['required', 'string', 'max:255'],
            'location_metadata.city_name' => ['required', 'string', 'max:255'],
            'image_files' => ['nullable', 'array'],
            'description' => ['required', 'string', 'max:65535'],
            'contract_no' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'string', 'date_format:Y-m-d'],
            'pic_name' => ['required', 'string', 'max:255'],
            'pic_phone_number' => ['required', 'string', 'min:10', 'max:15'],
        ]);
        $input['incident_date'] = CarbonImmutable::make($input['incident_date']);
        $dto = new AddInsuranceClaimSubmissionByUserV2RequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id(),
            ]);
        $service->execute($dto);

        return $this->responseOk();
    }
}
