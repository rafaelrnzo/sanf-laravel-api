<?php

namespace Sanf\External\Modules\User\Controllers;

use Illuminate\Http\Request;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\CoreAccountAvailabilityTransformer;
use Sanf\Api\Modules\User\Transformers\RegisterCoreAccountTransformer;
use Sanf\Core\Modules\User\Dtos\RegisterCoreAccountRequestDto;
use Sanf\Core\Modules\User\Services\BrowseCoreAccountAvailabilityService;
use Sanf\Core\Modules\User\Services\RegisterCoreAccountService;

class CoreAccountController extends RestApiController
{
    public function browseByScanina(Request $request, BrowseCoreAccountAvailabilityService $service)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'max:128'],
        ]);

        $result = $service->execute($request->get('email'));

        return fractal($result)
            ->transformWith(CoreAccountAvailabilityTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }

    public function addByScanina(Request $request, RegisterCoreAccountService $service)
    {
        $input = $this->validate($request, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:128'],
            'telephone' => 'required|max:16|regex:/^[0-9a-zA-Z-()\h\-]+$/',
            'handphone' => ['required', 'max:13', 'regex:/^[0-9]+$/'],
        ]);

        $registerRequestDto = new RegisterCoreAccountRequestDto($input);

        $result = $service->execute($registerRequestDto);

        return fractal($result)
            ->transformWith(RegisterCoreAccountTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }
}
