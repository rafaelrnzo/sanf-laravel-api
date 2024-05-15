<?php

namespace Sanf\External\Modules\User\Controllers;

use Illuminate\Http\Request;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\UserAvailabilityTransformer;
use Sanf\Core\Modules\User\Services\BrowseUserAvailabilityService;

class UserAvailabilityController extends RestApiController
{
    public function browseByScanina(Request $request, BrowseUserAvailabilityService $service)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'max:128'],
        ]);

        $result = $service->execute($request->get('email'));

        return fractal($result)
            ->transformWith(UserAvailabilityTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }
}
