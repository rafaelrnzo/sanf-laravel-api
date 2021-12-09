<?php


namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\AccountReceivableContractTransformer;
use Sanf\Core\Modules\Contract\Dto\AccountReceivableContractDto;

class AccountReceivableController extends RestApiController
{
    public function getList(Guard $auth, Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest'],
        ]);

        $input['currency_type'] = $request->header('Current-Type');

        $dto = new AccountReceivableContractDto($input);

        $result = (object)[
            'data' => [
                (object)[
                    'outstanding_amount' => '15000000',
                    'paid_amount' => '5000000',
                    'due_date' => '2021-12-01',
                    'installment' => '90000',
                    'registration_no' => '0721009890',
                    'contract_no' => '21KON98003',
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

        return fractal($result->data, AccountReceivableContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}