<?php

namespace Sanf\External\Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Setting\Dtos\UpdateOnBoardingDto;
use Sanf\Core\Modules\Setting\Services\BrowseOnBoardingService;
use Sanf\Core\Modules\Setting\Services\UpdateOnBoardingService;
use Sanf\External\Modules\Setting\Transformers\BrowseOnBoardingTransformer;

class OnBoardingByExternalController extends RestApiController
{
    public function getBrowse(Request $request, BrowseOnBoardingService $service)
    {
        $input = $this->validate($request, [
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['asc', 'desc',])],
        ]);

        $dto = (object)[
            'limit' => $input['limit'] ?? null,
            'sortBy' => $input['sort_by'] ?? null,
        ];
        $result = $service->execute($dto);

        return fractal($result, BrowseOnBoardingTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }

    public function postUpdate(
        $xid,
        Request $request,
        UpdateOnBoardingService $service
    ) {
        $inputs = $this->validate($request, [
            'title' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:200',
            'image_file' => 'nullable|image|mimetypes:image/png,image/jpeg,image/jpg,image/svg|max:2000',
        ]);

        $dto = new UpdateOnBoardingDto(array_merge($inputs, ['xid' => $xid]));
        $service->execute($dto);

        return $this->responseOk();
    }
}
