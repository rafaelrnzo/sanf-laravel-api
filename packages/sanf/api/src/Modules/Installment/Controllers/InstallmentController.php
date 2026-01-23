<?php

namespace Sanf\Api\Modules\Installment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Installment\Transformers\InstallmentDetailTransformer;
use Sanf\Api\Modules\Installment\Transformers\InstallmentListTransformer;
use Sanf\Core\Modules\Installment\Payloads\BrowseInstallmentPayload;
use Sanf\Core\Modules\Installment\Payloads\FindInstallmentPayload;
use Sanf\Core\Modules\Installment\Services\SummaryInstallmentService;
use Sanf\Core\Modules\Installment\UseCases\BrowseInstallmentUseCase;
use Sanf\Core\Modules\Installment\UseCases\FindInstallmentUseCase;

final class InstallmentController extends RestApiController
{
    public function summary(SummaryInstallmentService $service)
    {
        $summary = $service->execute();

        return $this->responseOk('Success', [
            'installment_count' => optional($summary)->jumlah_tagihan ?? 0,
            'total_amount' => optional($summary)->total_tagihan ?? 0,
        ]);
    }

    public function list(Request $request, Guard $auth, string $xid, BrowseInstallmentUseCase $useCase)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'sort_by' => ['nullable', 'string', 'in:due_date_latest,due_date_oldest'],
            'period_type' => ['nullable', 'string', 'in:current_month,next_month'],
            'contract_no' => ['nullable', 'string'],
            'due_date_after' => ['nullable', 'integer'],
        ]);

        $dto = new BrowseInstallmentPayload([
            'profileXid' => $xid,
            'userId' => $auth->id(),
            'skip' => $input['skip'] ?? 0,
            'limit' => $input['limit'] ?? 10,
            'sortBy' => $input['sort_by'] ?? 'due_date_latest',
            'periodType' => $input['period_type'] ?? 'current_month',
            'contractNo' => $input['contract_no'] ?? null,
            'dueDateAfter' => $input['due_date_after'] ?? null,
        ]);

        $result = $useCase->execute($dto);

        return fractal($result->data)
            ->transformWith(InstallmentListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function detail(Request $request, Guard $auth, string $xid, string $contractNo, string $dueDate, FindInstallmentUseCase $useCase)
    {
        $dto = new FindInstallmentPayload([
            'contractNo' => $contractNo,
            'dueDate' => $dueDate,
            'userId' => $auth->id(),
            'profileXid' => $xid,
        ]);

        $result = $useCase->execute($dto);

        $fractal = fractal($result, InstallmentDetailTransformer::class);

        $payload = $fractal->toArray();

        return $this->responseOk('OK', $payload['data'] ?? $payload);
    }
}
