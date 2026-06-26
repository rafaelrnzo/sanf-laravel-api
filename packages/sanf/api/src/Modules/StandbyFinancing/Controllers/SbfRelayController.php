<?php

namespace Sanf\Api\Modules\StandbyFinancing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SbfRelayController extends RestApiController
{
    public function plafond(string $custId, SanfApiService $service): JsonResponse
    {
        $service->setUser($custId);

        return $this->relay(fn () => $service->getPlafond($custId), 'plafond');
    }

    public function plafondList(Request $request, SanfApiService $service): JsonResponse
    {
        $input = $this->validate($request, ['cust_id' => ['required', 'string', 'max:50']]);
        $service->setUser($input['cust_id']);

        return $this->relay(fn () => $service->getPlafondListSbf($input['cust_id']), 'plafond_list');
    }

    public function plafondDetail(string $noPlafond, SanfApiService $service): JsonResponse
    {
        return $this->relay(fn () => $service->getPlafondDetailSbf($noPlafond), 'plafond_detail');
    }

    public function bankAccount(Request $request, SanfApiService $service): JsonResponse
    {
        $input = $this->validate($request, ['cust_id' => ['required', 'string', 'max:50']]);
        $service->setUser($input['cust_id']);

        return $this->relay(fn () => $service->getBankAccount($input['cust_id']), 'bank_account');
    }

    public function documents(Request $request, SanfApiService $service): JsonResponse
    {
        $input = $this->validate($request, ['cust_id' => ['required', 'string', 'max:50']]);
        $service->setUser($input['cust_id']);

        return $this->relay(fn () => $service->getDocumentList($input['cust_id']), 'documents');
    }

    public function pencairan(Request $request, SanfApiService $service): JsonResponse
    {
        $input = $this->validate($request, [
            'cust_id' => ['required', 'string', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $service->setUser($input['cust_id']);

        return $this->relay(
            fn () => $service->getListPencairan($input['cust_id'], (int) ($input['page'] ?? 1)),
            'pencairan'
        );
    }

    public function pencairanDetail(string $recapId, SanfApiService $service): JsonResponse
    {
        return $this->relay(fn () => $service->getDetailPencairan($recapId), 'pencairan_detail');
    }

    private function relay(callable $callback, string $operation): JsonResponse
    {
        try {
            return response()->json($callback());
        } catch (\Throwable $exception) {
            Log::error('SBF relay failed', [
                'operation' => $operation,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi core system',
            ], 502);
        }
    }
}
