<?php

namespace Sanf\External\Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\Setting\Dto\AddFrequentlyAskQuestionDto;
use Sanf\Core\Modules\Setting\Dto\UpdateFrequentlyAskQuestionDto;
use Sanf\Core\Modules\Setting\Services\AddFrequentlyAskQuestionService;
use Sanf\Core\Modules\Setting\Services\BrowseFrequentlyAskQuestionService;
use Sanf\Core\Modules\Setting\Services\DeleteFrequentlyAskQuestionService;
use Sanf\Core\Modules\Setting\Services\UpdateFrequentlyAskQuestionService;
use Sanf\External\Modules\Setting\Transformers\BrowseFrequentlyAskQuestionTransformer;

class FrequentlyAskQuestionByExternalController extends RestApiController
{
    public function getBrowse(Request $request, BrowseFrequentlyAskQuestionService $service)
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

        return fractal($result->data, BrowseFrequentlyAskQuestionTransformer::class)
            ->paginateWith( new LazyPaginatorAdapter($result->paginate));
    }

    public function postAdd(
        Request $request,
        AddFrequentlyAskQuestionService $service
    ) {
        $inputs = $this->validate($request, [
            'category_id' => 'required|integer',
            'title' => 'required|string|max:64',
            'description' => 'required|string',
            'is_popular' => 'required|boolean',
            'order' => 'required|numeric',
        ]);

        $dto = new AddFrequentlyAskQuestionDto($inputs);

        $service->execute($dto);

        return $this->responseOk();
    }

    public function putUpdate(
        $xid,
        Request $request,
        UpdateFrequentlyAskQuestionService $service
    ) {
        $inputs = $this->validate($request, [
            'category_id' => 'required|integer',
            'title' => 'required|string|max:64',
            'description' => 'required|string',
            'is_popular' => 'required|boolean',
            'order' => 'required|numeric',
        ]);

        $dto = new UpdateFrequentlyAskQuestionDto(array_merge($inputs, ['xid' => $xid]));
        $service->execute($dto);

        return $this->responseOk();
    }

    public function delete(
        $xid,
        DeleteFrequentlyAskQuestionService $service
    ) {

        $service->execute((object) ['xid' => $xid]);

        return $this->responseOk();
    }
}
