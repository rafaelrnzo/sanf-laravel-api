<?php

namespace Sanf\Api\Modules\News;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\News\GetListNewsDto;
use Sanf\Core\Modules\News\GetListNewsService;

class NewsController extends RestApiController
{
    public function getList(Request $request, GetListNewsService $service)
    {
        $this->validate($request, [
            'timestamp' => ['nullable', 'integer', 'min:0', 'max:99999999999'],
            'keyword' => 'nullable|string',
            'skip' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['oldest', 'latest', 'title', 'title_desc'])],
        ]);

        $dto = new GetListNewsDto([
            'timestamp' => (int) $request->input('timestamp'),
            'keyword' => $request->input('keyword'),
            'skip' => $request->input('skip'),
            'limit' => $request->input('limit'),
            'sort_by' => $request->input('sort_by') ?? 'latest',
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, NewsItemTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
