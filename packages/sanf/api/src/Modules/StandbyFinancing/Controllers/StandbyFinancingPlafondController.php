<?php

namespace Sanf\Api\Modules\StandbyFinancing\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingCustomerAccessService;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingPlafondService;

class StandbyFinancingPlafondController extends RestApiController
{
    public function header(
        Guard $auth,
        string $cust_id,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingPlafondService $plafondService
    ) {
        $accessService->assertCanAccess($auth->user(), $cust_id);

        return response()->json($plafondService->header($cust_id));
    }

    public function listSbf(
        Guard $auth,
        Request $request,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingPlafondService $plafondService
    ) {
        $customerId = $accessService->resolveCustomerId($auth->user(), $request->query('cust_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'List plafond SBF ditemukan',
            'data' => $plafondService->listSbf($customerId),
        ]);
    }

    public function detailSbf(
        Guard $auth,
        Request $request,
        string $no_plafond,
        StandbyFinancingCustomerAccessService $accessService,
        StandbyFinancingPlafondService $plafondService
    ) {
        $customerId = $accessService->resolveCustomerId($auth->user(), $request->query('cust_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Detail plafond SBF ditemukan',
            'data' => $plafondService->detailSbf($customerId, $no_plafond),
        ]);
    }
}
