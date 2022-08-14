<?php

namespace Sanf\Api\Modules\OnBoarding;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Setting\Transformers\BrowseOnBoardingTransformer;
use Sanf\Core\Modules\Setting\Services\BrowseOnBoardingService;

class OnBoardingController extends RestApiController
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
}
