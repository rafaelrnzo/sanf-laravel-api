<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\ContractOfFinancingUnitSubmissionTransformer;
use Sanf\Api\Modules\Contract\Transformers\FinancingUnitSubmissionContractTransformer;
use Sanf\Core\Modules\Contract\Dto\ContractOfFinancingUnitSubmissionDto;
use Sanf\Core\Modules\Contract\Dto\CreateFinancingUnitSubmissionDto;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitSubmissionDto;

class FinancingUnitSubmissionController extends RestApiController
{
    public function getContractList(Guard $auth, Request $request)
    {
        $input = $this->validate($request, [
            'contract_no' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $dto = new ContractOfFinancingUnitSubmissionDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'contract_no' => '21KON98010',
                    'created_at' => '2021-12-06 12:12:12'
                ]
            ],
            'paginate' => (object)[
                'total' => 1,
                'count' => 1,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];

        return fractal($result->data, ContractOfFinancingUnitSubmissionTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getFinancingUnit(Guard $auth, $contract_no, Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $input['contract_no'] = $contract_no;

        $dto = new FinancingUnitSubmissionDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'serial_no' => 'ZX12387SJKSD',
                    'brand_type_model' => 'KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7',
                    'provider_name' => 'Penyedia 1',
                    'year' => '2021',
                    'location_metadata' => (object)[
                        'city_id' => '001001001',
                        'city_name' => 'Jakarta Utara'
                    ],
                    'status' => (object)[
                        'id' => 10,
                        'name' => 'Diproses'
                    ],
                    'submitted_location_metadata' => (object)[
                        'city_id' => '001001002',
                        'city_name' => 'Jakarta Selatan'
                    ]
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

        return fractal($result->data, FinancingUnitSubmissionContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function updateLocation(Guard $auth, $contract_no, $serial_no, Request $request)
    {
        $input = $this->validate($request, [
            'location_metadata.city_id' => ['required', 'string', 'max:255'],
            'location_metadata.city_name' => ['required', 'string', 'max:255'],
        ]);

        $input['contract_no'] = $contract_no;
        $input['serial_no'] = $serial_no;

        $dto = new CreateFinancingUnitSubmissionDto($input);

        return $this->responseOk();
    }
}
