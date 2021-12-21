<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Insurance\Transformers\MyInsuranceClaimSubmissionSimpleTransformer;
use Sanf\Api\Modules\Insurance\Transformers\MyInsuranceClaimSubmissionTransformer;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\BrowseInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Services\AddInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\BrowseInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\ReadInsuranceClaimSubmissionByUserService;

final class InsuranceClaimSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseInsuranceClaimSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'timestamp' => ['nullable', 'integer'],
        ]);
        $dto = new BrowseInsuranceClaimSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyInsuranceClaimSubmissionSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getRead(Guard $auth, $xid, $submissionXid, ReadInsuranceClaimSubmissionByUserService $service)
    {
        $dto = new ReadInsuranceClaimSubmissionByUserRequestDto([
            'profileXid' => $xid,
            'xid' => $submissionXid,
            'userId' => $auth->id()
        ]);
        $result = $service->execute($dto);
        return fractal($result, new MyInsuranceClaimSubmissionTransformer());
    }

    public function postAdd(Guard $auth, Request $request, $xid, AddInsuranceClaimSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
//            'financing_unit' => ['required', 'array'],
//            'financing_unit.serial_no' => ['required', 'string', 'max:255'],
//            'financing_unit.polis_no' => ['required', 'string', 'max:255'],
//            'financing_unit.brand_type_model' => ['required', 'string', 'max:255'],
//            'financing_unit.year' => ['required', 'string', 'date_format:Y'],
//            'image_files' => ['nullable', 'array'],
//            'description' => ['required', 'string', 'max:65535'],
//            'incident_date' => ['required', 'string', 'date_format:Y-m-d'],
        ]);
        $dto = new AddInsuranceClaimSubmissionByUserRequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id()
            ]);
        $service->execute($dto);
        return $this->responseOk();
    }
}
