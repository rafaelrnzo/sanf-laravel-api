<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Prepayment\Transformers\PrepaymentSimulationTransformer;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSimulationByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSimulationByUserService;

final class PrepaymentSimulationByUserController extends RestApiController
{
    public function postAdd(Guard $auth, Request $request, AddPrepaymentSimulationByUserService $service)
    {
        $input = $this->validate($request, [
            'contract_no' => ['required', 'string', 'max:255'],
            'prepayment_date' => ['required', 'string', 'date_format:Y-m-d'],
            'is_download_pdf' => ['nullable', 'boolean'],
        ]);
        $input['prepayment_date'] = CarbonImmutable::make($input['prepayment_date']);
        $dto = new AddPrepaymentSimulationByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);
        return fractal($result, PrepaymentSimulationTransformer::class);
    }
}
