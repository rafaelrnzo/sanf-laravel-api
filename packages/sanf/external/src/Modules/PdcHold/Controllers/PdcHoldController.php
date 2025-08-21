<?php

namespace Sanf\External\Modules\PdcHold\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\PdcHold\Dtos\UpdateStatusPdcSubmissionByCoreRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Services\UpdatePdcSubmissionByCoreService;
use Sanf\External\Modules\PdcHold\Transformers\WebhookPdcHoldSubmissionTransformer;

/**
 * @since CR2025
 */
class PdcHoldController extends RestApiController
{
    public function updateStatus(
        Request $request,
        UpdatePdcSubmissionByCoreService $service
    ) {
        $input = $this->validate($request, [
            'metadata.cust_id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'metadata.pdc_hold_xid' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'payload.status' => ['required', 'string', Rule::in(PdcHoldStatusEnum::ALL_CORE_STATUS)],
        ]);

        $dto = new UpdateStatusPdcSubmissionByCoreRequestDto([
            'custId' => data_get($input, 'metadata.cust_id'),
            'pdcHoldXid' => data_get($input, 'metadata.pdc_hold_xid'),
            'status' => data_get($input, 'payload.status'),
        ]);

        $result = $service->execute($dto);

        return fractal($result, new WebhookPdcHoldSubmissionTransformer());
    }
}
