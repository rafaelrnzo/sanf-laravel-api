<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\BrowseDistrictTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseESignDocumentTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseProvinceTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseSubDistrictTransformer;
use Sanf\Api\Modules\Contract\Transformers\GenerateSignUrlTransformer;
use Sanf\Api\Modules\Contract\Transformers\GetESignUserTransformer;
use Sanf\Core\Modules\Contract\Dto\AddESignUserDto;
use Sanf\Core\Modules\Contract\Dto\BrowseDistrictDto;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dto\BrowseProvinceDto;
use Sanf\Core\Modules\Contract\Dto\BrowseSubDistrictDto;
use Sanf\Core\Modules\Contract\Dto\UpdateESignDocumentStatusDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Services\AddESignUserService;
use Sanf\Core\Modules\Contract\Services\BrowseDistrictService;
use Sanf\Core\Modules\Contract\Services\BrowseESignDocumentService;
use Sanf\Core\Modules\Contract\Services\BrowseProvinceService;
use Sanf\Core\Modules\Contract\Services\BrowseSubDistrictService;
use Sanf\Core\Modules\Contract\Services\GenerateSignUrlService;
use Sanf\Core\Modules\Contract\Services\GetESignUserCheckService;
use Sanf\Core\Modules\Contract\Services\GetESignUserService;
use Sanf\Core\Modules\Contract\Services\ResendESignVerificationService;
use Sanf\Core\Modules\Contract\Services\SendESignDocumentViaEmailService;
use Sanf\Core\Modules\Contract\Services\SycnESignDocumentSignService;
use Sanf\Core\Modules\Contract\Services\UpdateESignDocumentStatusService;
use Spatie\Fractalistic\ArraySerializer;

final class ESignDocumentByUserController extends RestApiController
{
    public function getUser(
        Guard $auth,
        GetESignUserService $service
    ) {
        $dto = (object) ['user_id' => $auth->id()];

        $result = $service->execute($dto);

        return fractal($result, GetESignUserTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function getBrowse(
        Guard $auth,
        Request $request,
        $xid,
        BrowseESignDocumentService $service,
        SycnESignDocumentSignService $syncService,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'status_id' => ['nullable', 'integer', Rule::in(ESignContractStatusEnum::ALL)],
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
            'timestamp' => ['nullable', 'integer'],
        ]);

        $dto = new BrowseESignDocumentDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        $transactionalService = new TransactionalApplicationService($syncService, $transactionalSession);
        $transactionalService->execute($result);

        return fractal($result->data, BrowseESignDocumentTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postRegistration(
        Guard $auth,
        Request $request,
        $xid,
        AddESignUserService $service
    ) {
        $input = $this->validate($request, [
            'email' => [
                'required',
                'email',
                'string',
                'max:255',
                Rule::unique('user_tekenaja', 'email')
                    ->where('status_id', ESignRegistrationStatusEnum::COMPLETE)
                    ->ignore($auth->id(), 'user_id'),
            ],
            'msisdn' => 'required|max:13|regex:/^[0-9]+$/',
            'nik' => [
                'required',
                'string',
                'max:255',
                Rule::unique('user_tekenaja', 'nik')
                    ->where('status_id', ESignRegistrationStatusEnum::COMPLETE)
                    ->ignore($auth->id(), 'user_id'),
            ],
            'full_name' => 'required|string|max:255',
            'pob' => 'required|string|max:255',
            'dob' => 'required|string|date_format:Y-m-d',
            'gender' => 'required|integer|in:0,1',
            'province_id' => 'required|integer|digits_between:1,1000',
            'district_id' => 'required|integer|digits_between:1,1000',
            'sub_district_id' => 'required|integer|digits_between:1,1000',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|integer|digits_between:1,1000',
            'selfie_file' => 'nullable|string|max:255',
            'identity_file' => 'nullable|string|max:255',
        ]);

        $dto = new AddESignUserDto($input + ['user_id' => $auth->id()]);

        $service->execute($dto);

        return $this->responseOk();
    }

    public function getProvinces(
        Guard $auth,
        Request $request,
        $xid,
        BrowseProvinceService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:asc,desc'],
        ]);

        $dto = new BrowseProvinceDto($input + ['user_id' => $auth->id()]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseProvinceTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDistricts(
        Guard $auth,
        Request $request,
        $xid,
        $provinceXid,
        BrowseDistrictService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:asc,desc'],
        ]);

        $dto = new BrowseDistrictDto($input + [
            'province_id' => $provinceXid,
            'user_id' => $auth->id(),
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseDistrictTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getSubDistricts(
        Guard $auth,
        Request $request,
        $xid,
        $provinceXid,
        $districtXid,
        BrowseSubDistrictService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:asc,desc'],
        ]);

        $dto = new BrowseSubDistrictDto($input + [
            'province_id' => $provinceXid,
            'district_id' => $districtXid,
            'user_id' => $auth->id(),
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseSubDistrictTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postRegistrationCheck(
        Guard $auth,
        Request $request,
        $xid,
        GetESignUserCheckService $service
    ) {
        $input = $this->validate($request, [
            'email' => 'required|email|string|max:255',
            'nik' => 'required|string|max:255',
        ]);

        $dto = (object) [
            'nik' => $input['nik'],
            'email' => $input['email'],
            'userId' => $auth->id(),
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

    public function postResendVerification(
        Guard $auth,
        Request $request,
        $xid,
        ResendESignVerificationService $service
    ) {
        $input = $this->validate($request, [
            'email' => 'required|email|string|max:255',
            'nik' => 'required|string|max:255',
        ]);

        $dto = (object) [
            'nik' => $input['nik'],
            'email' => $input['email'],
            'userId' => $auth->id(),
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

    public function postGenerateSignUrl(
        Guard $auth,
        Request $request,
        $xid,
        $document_id,
        GenerateSignUrlService $service
    ) {
        $input = $this->validate($request, [
            'email' => 'required|email|string|max:255',
        ]);

        $dto = (object) [
            'documentId' => $document_id,
            'email' => $input['email'],
            'userId' => $auth->id(),
        ];

        $result = $service->execute($dto);

        return fractal($result, GenerateSignUrlTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function postDocumentSigned(
        Guard $auth,
        Request $request,
        $xid,
        $document_id,
        UpdateESignDocumentStatusService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'email' => 'required|email|max:255',
        ]);

        $dto = new UpdateESignDocumentStatusDto([
            'email' => $input['email'],
            'documentId' => $document_id,
            'userId' => $auth->id(),
        ]);

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $transactionalService->execute($dto);

        return $this->responseOk();
    }

    public function postSendDocumentViaEmail(
        Guard $auth,
        Request $request,
        $xid,
        $document_id,
        SendESignDocumentViaEmailService $service
    ) {
        $input = $this->validate($request, [
            'email' => 'required|email|max:255',
        ]);

        $dto = (object) [
            'email' => $input['email'],
            'documentId' => $document_id,
            'userId' => $auth->id(),
        ];

        $service->execute($dto);

        return $this->responseOk();
    }
}
