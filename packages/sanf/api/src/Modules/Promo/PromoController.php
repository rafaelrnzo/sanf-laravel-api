<?php

namespace Sanf\Api\Modules\Promo;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\Promo\GetListPromoDto;
use Sanf\Core\Modules\Promo\GetListPromoService;

class PromoController extends RestApiController
{

    public function getList(Request $request, GetListPromoService $service)
    {
        $this->validate($request, [
            'skip' => 'nullable',
            'limit' => 'nullable',
            'sort_by' => ['nullable', Rule::in(['oldest', 'latest',])],
        ]);

        $dto = new GetListPromoDto([
            'skip' => $request->input('skip'),
            'limit' => ($request->input('limit')),
            'sort_by' => $request->input('sort_by') ?? 'latest',
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, PromoItemTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

}