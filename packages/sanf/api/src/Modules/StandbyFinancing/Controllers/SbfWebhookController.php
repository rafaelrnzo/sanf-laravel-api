<?php

namespace Sanf\Api\Modules\StandbyFinancing\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\StandbyFinancing\UseCases\ProcessSbfStatusWebhookUseCase;

class SbfWebhookController extends RestApiController
{
    public function handleStatus(
        Request $request,
        ProcessSbfStatusWebhookUseCase $useCase,
    ) {
        $formData = $this->validate($request, [
            'event_id' => ['required', 'string', 'max:32'],
            'event_type' => ['required', 'string'],
            'data' => ['required', 'array'],
            'data.status' => ['required', 'string'],
            'data.recap_id_b2b' => ['required', 'string'],
            'data.client_xid' => ['nullable', 'string'],
        ]);

        $result = $useCase->handle($formData);

        return $this->responseOk('Success', ['result' => $result]);
    }
}
