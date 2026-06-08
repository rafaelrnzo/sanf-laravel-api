<?php

namespace Sanf\Api\Modules\StandbyFinancing\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Sanf\Core\Modules\StandbyFinancing\Enums\StandbyFinancingDocumentEnum;
use Sanf\Core\Modules\StandbyFinancing\Repositories\StandbyFinancingRepositoryInterface;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingBankAccountService;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingCustomerAccessService;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingResponseService;
use Sanf\Core\Modules\StandbyFinancing\Services\SubmitStandbyFinancingService;

class StandbyFinancingController
{
    public function checkInvoice(
        Guard $auth,
        Request $request,
        StandbyFinancingCustomerAccessService $accessService,
        SubmitStandbyFinancingService $submitService
    ) {
        $payload = $this->validated($request->all(), [
            'nomor_invoice' => ['required', 'string'],
            'total_invoice' => ['required', 'numeric'],
            'noplafond' => ['required', 'string'],
            'cust_id' => ['nullable', 'string'],
        ]);
        $customerId = $accessService->resolveCustomerId($auth->user(), $payload['cust_id'] ?? $request->query('cust_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice valid dan dapat diproses.',
            'data' => $submitService->checkInvoice($customerId, $payload),
        ]);
    }

    public function bankAccount(
        Guard $auth,
        Request $request,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingBankAccountService $bankAccountService
    ) {
        $customerId = $accessService->resolveCustomerId($auth->user(), $request->query('cust_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account list retrieved successfully',
            'data' => $bankAccountService->listForCustomer($customerId),
        ]);
    }

    public function document()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Document list retrieved successfully',
            'data' => StandbyFinancingDocumentEnum::RULES,
        ]);
    }

    public function store(
        Guard $auth,
        Request $request,
        StandbyFinancingCustomerAccessService $accessService,
        SubmitStandbyFinancingService $submitService
    ) {
        $payload = $this->validated($request->all(), [
            'cust_id' => ['nullable', 'string'],
            'no_plafond' => ['required', 'string'],
            'period_start' => ['required', 'date_format:Y-m-d'],
            'period_end' => ['required', 'date_format:Y-m-d'],
            'tenor' => ['required', 'integer'],
            'supplier' => ['required', 'array'],
            'bank_account' => ['required', 'array'],
            'dokuments' => ['nullable', 'array'],
            'documents' => ['nullable', 'array'],
        ]);
        $customerId = $accessService->resolveCustomerId($auth->user(), $payload['cust_id'] ?? $request->query('cust_id'));
        $application = $submitService->submit($customerId, $request->all(), $accessService->actor($auth->user()));

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan standby financing berhasil dikirim.',
            'data' => [
                'recap_id_b2b' => $application->recap_id_b2b,
                'state_code' => $application->state_code,
            ],
        ]);
    }

    public function list(
        Guard $auth,
        Request $request,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingRepositoryInterface $repository,
        StandbyFinancingResponseService $responseService
    ) {
        $customerId = $accessService->resolveCustomerId($auth->user(), $request->query('cust_id'));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, (int) $request->query('per_page', $request->query('limit', 10)));
        $items = $repository->browseByCustomer($customerId, $page, $perPage);
        $total = $repository->countByCustomer($customerId);

        return response()->json([
            'status' => 'success',
            'message' => 'List pengajuan standby financing',
            'data' => array_map(function ($application, $index) use ($responseService, $page, $perPage) {
                return $responseService->listItem($application, (($page - 1) * $perPage) + $index + 1);
            }, $items, array_keys($items)),
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
                'next_page' => ($page * $perPage) < $total ? $page + 1 : null,
            ],
        ]);
    }

    public function detail(
        Guard $auth,
        Request $request,
        string $recap_id_b2b,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingRepositoryInterface $repository,
        StandbyFinancingResponseService $responseService
    ) {
        $customerId = $accessService->resolveCustomerId($auth->user(), $request->query('cust_id'));
        $application = $repository->findByRecapId($recap_id_b2b, $customerId);
        abort_if(is_null($application), 404, 'Standby financing request not found.');

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pengajuan standby financing',
            'data' => $responseService->detail($application),
        ]);
    }

    private function validated(array $payload, array $rules): array
    {
        return Validator::make($payload, $rules)->validate();
    }
}
