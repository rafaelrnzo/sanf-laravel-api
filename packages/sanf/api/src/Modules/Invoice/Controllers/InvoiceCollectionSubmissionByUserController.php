<?php

namespace Sanf\Api\Modules\Invoice\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Invoice\Transformers\MyInvoiceCollectionSubmissionSimpleTransformer;
use Sanf\Core\Modules\Invoice\Dtos\AddInvoiceCollectionSubmissionByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\BrowseInvoiceCollectionSubmissionByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\FinancingUnitRequestDto;
use Sanf\Core\Modules\Invoice\Enums\InvoiceCollectionSubmissionStatusEnum;
use Sanf\Core\Modules\Invoice\Services\AddInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\BrowseInvoiceCollectionSubmissionByUserService;

final class InvoiceCollectionSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseInvoiceCollectionSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'status_id' => ['nullable', 'integer', Rule::in(InvoiceCollectionSubmissionStatusEnum::ALL_STATUS)],
            'keyword' => ['nullable', 'string', 'max:255'],
        ]);
        $dto = new BrowseInvoiceCollectionSubmissionByUserRequestDto($input + [
                'userId' => $auth->id(),
                'profileXid' => $xid,
            ]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyInvoiceCollectionSubmissionSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postAdd(Guard $auth, Request $request, $xid, AddInvoiceCollectionSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'pickup_date' => ['required', 'string', 'date_format:Y-m-d'],
            'financing_units' => ['required', 'array'],
            'financing_units.*.contract_no' => ['required', 'string'],
            'financing_units.*.serial_no' => ['required', 'string'],
            'financing_units.*.brand_type_model' => ['required', 'string'],
            'financing_units.*.year' => ['required', 'string'],
        ]);
        $financingUnits = array_map(function ($item) {
            return new FinancingUnitRequestDto($item);
        }, $input['financing_units']);
        $dto = new AddInvoiceCollectionSubmissionByUserRequestDto([
            'pickupDate' => CarbonImmutable::createFromFormat('Y-m-d', $input['pickup_date']),
            'financing_units' => $financingUnits,
            'profileXid' => $xid,
            'userId' => $auth->id()
        ]);
        $service->execute($dto);
        return $this->responseOk();
    }
}
