<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\DetailContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\FinancingUnitContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\ListContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\SummaryBillContractTransformer;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitContractDto;
use Sanf\Core\Modules\Contract\Dto\ListContractDto;
use Sanf\Core\Modules\Contract\Dto\SummaryBillContractDto;
use Sanf\Core\Modules\Contract\Enums\ContractTypeEnum;
use Sanf\Core\Modules\Contract\Services\ListContractService;

class ContractController extends RestApiController
{
    public function getList(
        Guard $auth,
        Request $request,
        ListContractService $service
    ) {
        $input = $this->validate($request, [
            'contract_type' => ['nullable', 'in:active,settled'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new ListContractDto($input);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        switch ($input['contract_type']) {
            case ContractTypeEnum::SETTLED:
                $dto->contract_type = ContractTypeEnum::SETTLED_LABEL;
                break;
            case ContractTypeEnum::ACTIVE_LABEL:
            default:
                $dto->contract_type = ContractTypeEnum::ACTIVE_LABEL;
                break;
        }

        $result = $service->execute($dto);

        return fractal($result->data, ListContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetail(Guard $auth, $contract_no, Request $request)
    {
        // TODO remove this;
        $input = $this->validate($request, [
            'mock' => ['nullable', 'in:active,arrears,settled,']
        ]);
        $mock = $input['mock'] ?? 'active';

        $result = [
            'active' => (object)[
                'contract_at' => '2021-01-28',
                'contract_no' => '21KON98010',
                'currency_type' => 'IDR',
                'status' => (object)[
                    'id' => 1,
                    'name' => 'Aktif',
                ],
                'total_amount' => 11500999999,
                'total_installment' => 24,
                'total_outstanding_amount' => 500000000,
                'total_paid_amount' => 1500000000,
                'total_invoice' => 89098989,
                'due_at' => '2021-12-31',
                'installment_count' => 14,
                'financing' => (object)[
                    'due_at' => '2021-01-25',
                    'finished_at' => '2025-01-11',
                    'interest_percentage' => 12,
                    'facility' => (object)[
                        'id' => 1,
                        'name' => 'Investasi',
                    ],
                    'method' => (object)[
                        'id' => 2,
                        'name' => 'Pembelian dengan Pembayaran secara Angsuran',
                    ],
                    'total_tenor' => 60,
                ],
                'total_financing_unit' => 3,
            ],
            'arrears' => (object)[
                'contract_at' => '2021-01-28',
                'contract_no' => '21KON98010',
                'currency_type' => 'IDR',
                'status' => (object)[
                    'id' => 1,
                    'name' => 'Aktif',
                ],
                'total_amount' => 24000000000,
                'total_installment' => 24,
                'total_outstanding_amount' => 500000000,
                'total_paid_amount' => 1500000000,
                'total_invoice' => 178947978,
                'due_at' => '2021-01-25',
                'installment_count' => 14,
                'financing' => (object)[
                    'due_at' => '2021-01-25',
                    'finished_at' => '2025-01-11',
                    'interest_percentage' => 12,
                    'facility' => (object)[
                        'id' => 1,
                        'name' => 'Investasi',
                    ],
                    'method' => (object)[
                        'id' => 2,
                        'name' => 'Pembelian dengan Pembayaran secara Angsuran',
                    ],
                    'total_tenor' => 60
                ],
                'total_financing_unit' => 3,
            ],
            'settled' => (object)[
                'contract_at' => '2021-01-28',
                'contract_no' => '21KON98010',
                'currency_type' => 'IDR',
                'status' => (object)[
                    'id' => 2,
                    'name' => 'Selesai',
                ],
                'total_amount' => 11500999999,
                'total_installment' => 24,
                'total_outstanding_amount' => 0,
                'total_paid_amount' => 24000000000,
                'total_invoice' => 0,
                'due_at' => '2021-01-25',
                'installment_count' => 24,
                'financing' => (object)[
                    'due_at' => '2021-01-25',
                    'finished_at' => '2025-01-11',
                    'interest_percentage' => 12,
                    'facility' => (object)[
                        'id' => 1,
                        'name' => 'Investasi',
                    ],
                    'method' => (object)[
                        'id' => 2,
                        'name' => 'Pembelian dengan Pembayaran secara Angsuran',
                    ],
                    'total_tenor' => 60
                ],
                'total_financing_unit' => 3,
            ]
        ];

        return fractal($result[$mock], DetailContractTransformer::class);
    }

    public function getFinancingUnit(Guard $auth, $contract_no, Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $input['contract_no'] = $contract_no;

        $dto = new FinancingUnitContractDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'serial_no' => 'ZX12387SJKSD',
                    "brand_type_model" => "KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7",
                    'provider_name' => 'Penyedia 1',
                    'year' => '2021',
                ],
            ],
            'paginate' => (object)[
                'total' => 1,
                'count' => 1,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];

        return fractal($result->data, FinancingUnitContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPenalties(Guard $auth, $contract_no, Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $input['contract_no'] = $contract_no;

        $dto = new SummaryBillContractDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'due_at' => '2021-01-25',
                    'bill_amount' => 100000,
                    'penalty_amount' => 1000,
                    'currency_type' => 'IDR'
                ],
            ],
            'paginate' => (object)[
                'total' => 1,
                'count' => 1,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];

        return fractal($result->data, SummaryBillContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
