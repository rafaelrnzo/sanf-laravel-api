<?php

namespace Sanf\External\Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\Setting\Dto\UpdateFrequentlyAskQuestionCategoryDto;
use Sanf\Core\Modules\Setting\Services\AddFrequentlyAskQuestionCategoryService;
use Sanf\Core\Modules\Setting\Services\BrowseFrequentlyAskQuestionCategoryService;
use Sanf\Core\Modules\Setting\Services\DeleteFrequentlyAskQuestionCategoryService;
use Sanf\Core\Modules\Setting\Services\UpdateFrequentlyAskQuestionCategoryService;
use Sanf\External\Modules\Setting\Transformers\BrowseFrequentlyAskQuestionCategoryTransformer;

class FrequentlyAskQuestionCategoryByExternalController extends RestApiController
{
    public function getBrowse(Request $request, BrowseFrequentlyAskQuestionCategoryService $service)
    {
        $input = $this->validate($request, [
            'limit' => 'nullable|integer',
            'skip' => 'nullable|integer',
            'keyword' => 'nullable|string|max:255',
            'sort_by' => ['nullable', Rule::in(['asc', 'desc',])],
        ]);

        $dto = (object)[
            'keyword' => $input['keyword'] ?? null,
            'limit' => $input['limit'] ?? null,
            'skip' => $input['skip'] ?? null,
            'sortBy' => $input['sort_by'] ?? null,
        ];

        $result = $service->execute($dto);

        return fractal($result->data, BrowseFrequentlyAskQuestionCategoryTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postAdd(
        Request $request,
        AddFrequentlyAskQuestionCategoryService $service
    ) {
        $inputs = $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        $service->execute((object)['name' => $inputs['name'],]);

        return $this->responseOk();
    }

    public function putUpdate(
        $xid,
        Request $request,
        UpdateFrequentlyAskQuestionCategoryService $service
    ) {
        $inputs = $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        $dto = new UpdateFrequentlyAskQuestionCategoryDto(array_merge($inputs, ['xid' => $xid]));
        $service->execute($dto);

        return $this->responseOk();
    }

    public function delete(
        $xid,
        DeleteFrequentlyAskQuestionCategoryService $service
    ) {

        $service->execute((object) ['xid' => $xid]);

        return $this->responseOk();
    }
}
