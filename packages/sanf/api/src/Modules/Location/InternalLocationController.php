<?php


namespace Sanf\Api\Modules\Location;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Location\Transformers\LocationListTransformer;
use Sanf\Core\Modules\Location\Internal\GetListLocationDto;
use Sanf\Core\Modules\Location\Internal\GetListLocationService;
use Sanf\Core\Modules\Location\Internal\LocationEnum;

class InternalLocationController extends RestApiController
{

    public function provinces(Request $request, GetListLocationService $service)
    {
        $this->validate($request, [
            'xid' => 'nullable|string',
            'keyword' => 'nullable|string',
            'skip' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['oldest', 'latest', 'name_desc', 'name_asc'])],
        ]);

        $dto = new GetListLocationDto([
            'keyword' => $request->input('keyword'),
            'xid' => $request->input('xid'),
            'skip' => $request->input('skip'),
            'limit' => $request->input('limit'),
            'sort_by' => $request->input('sort_by') ?? 'oldest',
            'adm_area_id' => LocationEnum::PROVINCE_LV()->getValue()
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, LocationListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function cities(string $parent_xid, Request $request, GetListLocationService $service)
    {
        $this->validate($request, [
            'xid' => 'nullable|string',
            'keyword' => 'nullable|string',
            'skip' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['oldest', 'latest', 'name_desc', 'name_asc'])],
        ]);

        $dto = new GetListLocationDto([
            'parent_xid' => $parent_xid,
            'keyword' => $request->input('keyword'),
            'xid' => $request->input('xid'),
            'skip' => $request->input('skip'),
            'limit' => $request->input('limit'),
            'sort_by' => $request->input('sort_by') ?? 'oldest',
            'adm_area_id' => LocationEnum::CITY_LV()->getValue()
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, LocationListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
