<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSimulationByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSubmissionByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSubmissionByUserService;

final class PrepaymentSubmissionByUserController extends RestApiController
{
    public function postAdd(
        Guard $auth,
        Request $request,
        $xid,
        AddPrepaymentSubmissionByUserService $submissionService,
        AddPrepaymentSimulationByUserService $simulationService
    ) {
        $input = $this->validate($request, [
            'contract_no' => ['required', 'string', 'max:255'],
            'prepayment_date' => ['required', 'string', 'date_format:Y-m-d'],
        ]);

        $input['prepayment_date'] = CarbonImmutable::make($input['prepayment_date']);
        $dto = new AddPrepaymentSimulationByUserRequestDto($input + ['userId' => $auth->id()]);
        $prepaymentSimulation = $simulationService->execute($dto);

        $dto = new AddPrepaymentSubmissionByUserRequestDto([
                'profileXid' => $xid,
                'userId' => $auth->id(),
                'prepaymentSimulation' => $prepaymentSimulation
            ]);

        $submissionService->execute($dto);
        return $this->responseOk();
    }
}
