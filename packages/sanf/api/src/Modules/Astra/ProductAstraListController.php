<?php

namespace Sanf\Api\Modules\Astra;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\Astra\GetListProductAstraDto;
use Sanf\Core\Modules\Astra\GetListProductAstraService;

class ProductAstraListController extends RestApiController
{

    public function getList(Request $request, GetListProductAstraService $service)
    {
        $this->validate($request, [
            'skip' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['oldest', 'latest',])],
        ]);

        $dto = new GetListProductAstraDto([
            'skip' => $request->input('skip'),
            'limit' => $request->input('limit'),
            'sort_by' => $request->input('sort_by') ?? 'latest',
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, ProductAstraItemTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

}