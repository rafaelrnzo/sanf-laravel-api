<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\Contract\Transformers\ResponseESignUserTransformer;
use Sanf\Core\Database\IlluminateSodiumSession;
use Sanf\Core\Database\MultipleTransactionalSessionInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentSignDto;
use Sanf\Core\Modules\Contract\Dto\RequestESignRegisterFormDto;
use Sanf\Core\Modules\Contract\Dtos\CountESignDocumentAssigneeStatusRequestDto;
use Sanf\Core\Modules\Contract\Dtos\ESignDocumentCheckStatusRequestDto;
use Sanf\Core\Modules\Contract\Services\CountESignDocumentAssigneeStatusService;
use Sanf\Core\Modules\Contract\Services\ESignDocumentCheckStatusService;
use Sanf\Core\Modules\Contract\Services\ESignDocumentSignAdInsService;
use Sanf\Core\Modules\Contract\Services\ESignRegisterAdInsService;
use Sanf\Core\Modules\Contract\Services\SanfESignUserService;
use Sanf\Core\Services\MultipleTransactionalApplicationService;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentV2Controller extends RestApiController
{
    public function getUser(
        string $xid,
        SanfESignUserService $eSignSanfUserService,
        MultipleTransactionalSessionInterface $transactionalSession,
        Guard $auth
    ) {
        $dto = (object) [
            'profileXid' => $xid,
            'userId' => $auth->id(),
        ];

        $transactionalService = new MultipleTransactionalApplicationService($eSignSanfUserService, $transactionalSession);
        $eSignSanfUser = $transactionalService->execute($dto);

        return fractal($eSignSanfUser, ResponseESignUserTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function registration(
        Request $request,
        $xid,
        ESignRegisterAdInsService $eSignRegisterAdinsService,
        MultipleTransactionalSessionInterface $transactionalSession,
        Guard $auth
    ) {
        $input = $this->validate($request, [
            'email' => [
                'required',
                'email',
                'string',
                'max:255',
            ],
            'msisdn' => [
                'required',
                'string',
                'max:16',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+62|62|0)/', $value)) {
                        return $fail('The phone number must start with +62, 62, or 0.');
                    }
                    if (!preg_match('/^\+?[0-9]+$/', $value)) {
                        return $fail('The phone number must only contain numeric characters.');
                    }
                },
            ],
            'nik' => [
                'required',
                'string',
                'max:255',
            ],
            'full_name' => 'required|string|max:255',
            'pob' => 'required|string|max:255',
            'dob' => 'required|string|date_format:Y-m-d',
            'gender' => 'required|integer|in:0,1',
            'province_id' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'province_name' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'city_id' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'city_name' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'district_name' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'sub_district_name' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'address' => 'required|string|max:255',
            'postal_code' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'selfie_file' => 'required|string|max:255',
            'identity_file' => 'required|string|max:255',
            'password' => ['required', 'min:9', 'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>])[^\s]+$/', 'confirmed'],
            'password_confirmation' => ['required', 'min:9', 'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>])[^\s]+$/'],
        ]);

        $input['userId'] = $auth->id();
        $input['sanfId'] = $xid;
        $input['identityNo'] = $input['nik'];
        $input['province'] = $input['province_name'];
        $input['city'] = $input['city_name'];
        $input['district'] = $input['district_name'];
        $input['subDistrict'] = $input['sub_district_name'];

        $dto = new RequestESignRegisterFormDto($input);

        $transactionalService = new MultipleTransactionalApplicationService($eSignRegisterAdinsService, $transactionalSession);
        $transactionalService->execute($dto);

        return $this->responseOk();
    }

    public function signing(
        Request $request,
        $xid,
        $document_id,
        ESignDocumentSignAdInsService $eSignRegisterAdinsService,
        MultipleTransactionalSessionInterface $transactionalSession,
        Guard $auth
    ) {
        $input = $this->validate($request, [
            'email' => [
                'required',
                'email',
                'string',
                'max:255',
            ],
            'msisdn' => [
                'required',
                'string',
                'max:16',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+62|62|0)/', $value)) {
                        return $fail('The phone number must start with +62, 62, or 0.');
                    }
                    if (!preg_match('/^\+?[0-9]+$/', $value)) {
                        return $fail('The phone number must only contain numeric characters.');
                    }
                },
            ],
            'otp' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
        ]);

        $input['userId'] = $auth->id();
        $input['sanfId'] = $xid;
        $input['documentId'] = $document_id;
        $input['ipAddress'] = $request->ip();
        $input['userAgent'] = $request->header('User-Agent');

        $dto = new RequestESignDocumentSignDto($input);

        $transactionalService = new MultipleTransactionalApplicationService($eSignRegisterAdinsService, $transactionalSession);
        $transactionalService->execute($dto);

        return $this->responseOk();
    }

    public function getStats(
        Guard $auth,
        CountESignDocumentAssigneeStatusService $service,
        IlluminateSodiumSession $sodiumTransactionalSession,
        $xid
    )
    {
        $dto = new CountESignDocumentAssigneeStatusRequestDto([
            'userId' => $auth->id(),
            'sanfId' => $xid,
        ]);

        $sodiumTransactionalService = new TransactionalApplicationService($service, $sodiumTransactionalSession);
        $result = $sodiumTransactionalService->execute($dto);

        return $this->responseOk('Success', $result);
    }

    public function checkStatus(
        Guard $auth,
        $xid,
        $document_id,
        ESignDocumentCheckStatusService $eSignDocumentCheckStatusService,
        MultipleTransactionalSessionInterface $transactionalSession
    )
    {
        $dto = new ESignDocumentCheckStatusRequestDto([
            'userId' => $auth->id(),
            'sanfId' => $xid,
            'documentId' => $document_id,
        ]);

        $transactionalService = new MultipleTransactionalApplicationService($eSignDocumentCheckStatusService, $transactionalSession);
        $result = $transactionalService->execute($dto);

        return $this->responseOk('Success', $result->toArray());
    }
}
