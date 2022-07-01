<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\BrowseESignDocumentTransformer;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Services\BrowseESignDocumentService;

final class ESignDocumentByUserController extends RestApiController
{
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
}
