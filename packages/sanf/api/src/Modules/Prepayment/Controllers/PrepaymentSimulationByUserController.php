<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Prepayment\Transformers\PrepaymentSimulationTransformer;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSimulationByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\GetPdfPrepaymentSimulationRequestDto;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\GetPdfPrepaymentSimulationService;

final class PrepaymentSimulationByUserController extends RestApiController
{
    public function postAdd(
        Guard $auth,
        Request $request,
        AddPrepaymentSimulationByUserService $service,
        GetPdfPrepaymentSimulationService $downloadPrepaymentService
    ) {
        $input = $this->validate($request, [
            'contract_no' => ['required', 'string', 'max:255'],
            'prepayment_date' => ['required', 'string', 'date_format:Y-m-d'],
            'is_download_pdf' => ['nullable', 'boolean'],
        ]);
        $dto = new AddPrepaymentSimulationByUserRequestDto([
            'contractNo' => $input['contract_no'],
            'prepaymentDate' => CarbonImmutable::make($input['prepayment_date']),
            'userId' => $auth->id(),
            'isDownloadPdf' => $input['is_download_pdf'],
        ]);
        $simulationResult = $service->execute($dto);
        if ($dto->isDownloadPdf) {
            // Set dto for download service
            $dtoDownload = new GetPdfPrepaymentSimulationRequestDto([
                    'user_id' => $auth->id(),
                ] + $simulationResult->toArray());

            // Execute download service
            return $this->streamDownload(
                function () use ($downloadPrepaymentService, $dtoDownload) {
                echo $downloadPrepaymentService->execute($dtoDownload);
            },
                'Simulasi Pelunasan Dipercepat ' . date('d_m_y') . '.pdf'
            );
        }

        return fractal($simulationResult, PrepaymentSimulationTransformer::class);
    }
}
