<?php

namespace Sanf\Api\Modules\PdcHold\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\PdcHold\Transformers\PdcHoldReasonListTransformer;
use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldReasonRequestDto;
use Sanf\Core\Modules\PdcHold\Services\ListPdcHoldReasonService;

/**
 * @since CR2025
 */
class PdcHoldReasonController extends RestApiController
{
    public function getList(
        Request $request,
        ListPdcHoldReasonService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
        ]);
        $dto = new BrowsePdcHoldReasonRequestDto($input);
        $result = $service->execute($dto);

        return fractal($result->data)
            ->transformWith(PdcHoldReasonListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
