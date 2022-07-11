<?php

namespace Sanf\External\Modules\Contract;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Contract\Transformers\ESignUserRegisteredTransformer;
use Sanf\Core\Modules\Contract\Services\ESignUserRegisteredService;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentByExternalController extends RestApiController
{
    public function postHasVerified(Request $request, ESignUserRegisteredService $service)
    {
        $input  = $this->validate($request, [
            'email' => 'required|email|string|max:255',
        ]);

        $dto = (object) ['email' => $input['email']];

        $result = $service->execute($dto);
        $result->response_code = 'REGISTRATION_COMPLETE';

        return fractal($result, ESignUserRegisteredTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }
}
