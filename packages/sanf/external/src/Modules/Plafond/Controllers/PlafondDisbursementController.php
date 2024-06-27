<?php

namespace Sanf\External\Modules\Plafond\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\UseCases\UpdatePlafondDisbursementFromCoreUseCase;

class PlafondDisbursementController extends RestApiController
{
    public function update(
        Request $request,
        TransactionalSessionInterface $transactionalSession,
        UpdatePlafondDisbursementFromCoreUseCase $updateUseCase
    ) {
        $this->validate($request, [
            'metadata.cust_id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'metadata.plafond_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'metadata.disbursement_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'payload.status' => ['required', 'string', Rule::in([PlafondDisbursementStatusEnum::CORE_APPROVAL, PlafondDisbursementStatusEnum::CORE_REJECTED])],
            'payload.notes' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->get('payload')['status'] === PlafondDisbursementStatusEnum::CORE_REJECTED)],
        ]);

        $formRequest = (object) [
            'disbursementNo' => $request->get('metadata')['disbursement_no'],
            'plafondNo' => $request->get('metadata')['plafond_no'],
            'custId' => $request->get('metadata')['cust_id'],
            'status' => $request->get('payload')['status'],
            'notes' => $request->get('payload')['notes'] ?? null,
        ];

        $transactionalService = new TransactionalApplicationService($updateUseCase, $transactionalSession);
        $transactionalService->execute($formRequest);

        return $this->responseOk();
    }
}
