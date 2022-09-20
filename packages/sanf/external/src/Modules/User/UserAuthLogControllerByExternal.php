<?php

namespace Sanf\External\Modules\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Dto\BrowseRequestDto;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Services\BrowseUserDeletionAccountService;
use Sanf\External\Modules\User\Transformers\BrowseUserDeletionAccountTransformer;

class UserAuthLogControllerByExternal extends RestApiController
{
    public function getBrowse(Request $request, BrowseUserDeletionAccountService $service)
    {
        $input = $this->validate($request, [
            'keyboard' => 'nullable|string|max:255',
            'limit' => 'nullable|integer',
            'skip' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['asc', 'desc',])],
            'status_id' => ['nullable', Rule::in(UserAuthLogStatusEnum::ALL)]
        ]);

        $dto = new BrowseRequestDto($input);
        $dto->statusId = $input['status_id'] ?? UserAuthLogStatusEnum::SUBMIT;

        $result = $service->execute($dto);

        return fractal($result->data, BrowseUserDeletionAccountTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
