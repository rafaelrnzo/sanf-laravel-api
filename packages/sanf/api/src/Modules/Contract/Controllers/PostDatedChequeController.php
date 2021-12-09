<?php


namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\ContractPostDatedChequeTransformer;
use Sanf\Api\Modules\Contract\Transformers\PostDatedChequeTransformer;
use Sanf\Core\Modules\Contract\Dto\ContractPostDatedChequeDto;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeDto;

class PostDatedChequeController extends RestApiController
{
    public function getContractList(Guard $auth, Request $request)
    {
        $input = $this->validate($request, [
            'contract_no' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $dto = new ContractPostDatedChequeDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'contract_no' => '21KON98010',
                    'currency_type' => 'IDR',
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

        return fractal($result->data, ContractPostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPDCList(Guard $auth, $contract_no, Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $input['contract_no'] = $contract_no;

        $dto = new PostDatedChequeDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'pdc_no' => 'N0909PDC23',
                    'amount' => '2500000',
                    'currency_type' => 'IDR',
                    'submitted_date' => '2021-12-01',
                    'pdc_type' => 'PDC Installment',
                    'status' => (object)[
                        'id' => '001',
                        'name' => 'Diterima',
                    ]
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

        return fractal($result->data, PostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}