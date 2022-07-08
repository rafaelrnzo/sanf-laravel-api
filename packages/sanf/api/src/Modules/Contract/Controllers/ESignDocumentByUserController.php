<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\BrowseDistrictTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseESignDocumentTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseProvinceTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseSubDistrictTransformer;
use Sanf\Api\Modules\Contract\Transformers\GetESignUserTransformer;
use Sanf\Core\Modules\Contract\Dto\BrowseDistrictDto;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dto\BrowseProvinceDto;
use Sanf\Core\Modules\Contract\Dto\BrowseSubDistrictDto;
use Sanf\Core\Modules\Contract\Services\BrowseDistrictService;
use Sanf\Core\Modules\Contract\Services\BrowseESignDocumentService;
use Sanf\Core\Modules\Contract\Services\BrowseProvinceService;
use Sanf\Core\Modules\Contract\Services\BrowseSubDistrictService;
use Sanf\Core\Modules\Contract\Services\GetESignUserService;
use Spatie\Fractalistic\ArraySerializer;

final class ESignDocumentByUserController extends RestApiController
{
    public function getUser(
        Guard $auth,
        GetESignUserService $service
    ) {
        $dto = (object)['user_id' => $auth->id(),];

        $result = $service->execute($dto);

        return fractal($result, GetESignUserTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function getBrowse(
        Guard $auth,
        Request $request,
        $xid,
        BrowseESignDocumentService $service
    ) {
        $input = $this->validate($request, [
            'status_id' => ['required', 'integer', 'in:10,20,30',],
            'keyword' => ['nullable', 'string', 'max:255',],
            'skip' => ['nullable', 'integer', 'max:2147483647',],
            'limit' => ['nullable', 'integer', 'max:2147483647',],
            'sort_by' => ['nullable', 'in:earliest,latest',],
            'timestamp' => ['nullable', 'integer',],
        ]);

        $dto = new BrowseESignDocumentDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, BrowseESignDocumentTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getProvinces(
        Guard $auth,
        Request $request,
        $xid,
        BrowseProvinceService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255',],
            'skip' => ['nullable', 'integer', 'max:2147483647',],
            'limit' => ['nullable', 'integer', 'max:2147483647',],
            'sort_by' => ['nullable', 'in:asc,desc',],
        ]);

        $dto = new BrowseProvinceDto($input + ['user_id' => $auth->id()]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseProvinceTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDistricts(
        Guard $auth,
        Request $request,
        $xid,
        $provinceXid,
        BrowseDistrictService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255',],
            'skip' => ['nullable', 'integer', 'max:2147483647',],
            'limit' => ['nullable', 'integer', 'max:2147483647',],
            'sort_by' => ['nullable', 'in:asc,desc',],
        ]);

        $dto = new BrowseDistrictDto($input + [
            'province_id' => $provinceXid,
            'user_id' => $auth->id(),
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseDistrictTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getSubDistricts(
        Guard $auth,
        Request $request,
        $xid,
        $provinceXid,
        $districtXid,
        BrowseSubDistrictService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255',],
            'skip' => ['nullable', 'integer', 'max:2147483647',],
            'limit' => ['nullable', 'integer', 'max:2147483647',],
            'sort_by' => ['nullable', 'in:asc,desc',],
        ]);

        $dto = new BrowseSubDistrictDto($input + [
            'province_id' => $provinceXid,
            'district_id' => $districtXid,
            'user_id' => $auth->id(),
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, BrowseSubDistrictTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
