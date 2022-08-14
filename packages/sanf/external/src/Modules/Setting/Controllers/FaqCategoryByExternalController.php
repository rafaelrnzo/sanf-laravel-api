<?php

namespace Sanf\External\Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Setting\Dto\UpdateFaqCategoryDto;
use Sanf\Core\Modules\Setting\Services\AddFaqCategoryService;
use Sanf\Core\Modules\Setting\Services\BrowseFaqCategoryService;
use Sanf\Core\Modules\Setting\Services\DeleteFaqCategoryService;
use Sanf\Core\Modules\Setting\Services\UpdateFaqCategoryService;
use Sanf\External\Modules\Setting\Transformers\BrowseFaqCategoryTransformer;

class FaqCategoryByExternalController extends RestApiController
{
    public function getBrowse(Request $request, BrowseFaqCategoryService $service)
    {
        $input = $this->validate($request, [
            'limit' => 'nullable|integer',
            'skip' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['asc', 'desc',])],
        ]);

        $dto = (object)[
            'limit' => $input['limit'] ?? null,
            'skip' => $input['skip'] ?? null,
            'sortBy' => $input['sort_by'] ?? null,
        ];
        $result = $service->execute($dto);

        return fractal($result, BrowseFaqCategoryTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }

    public function postAdd(
        Request $request,
        AddFaqCategoryService $service
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
        UpdateFaqCategoryService $service
    ) {
        $inputs = $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        $dto = new UpdateFaqCategoryDto(array_merge($inputs, ['xid' => $xid]));
        $service->execute($dto);

        return $this->responseOk();
    }

    public function delete(
        $xid,
        DeleteFaqCategoryService $service
    ) {

        $service->execute((object) ['xid' => $xid]);

        return $this->responseOk();
    }
}
