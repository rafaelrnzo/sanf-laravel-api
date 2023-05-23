<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\PostUserAccountResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\PostUserRegisterRequestDto;
use Sanf\Core\Modules\Scanina\Services\GuzzleUserRegisterService;
use Spatie\Fractalistic\ArraySerializer;

class RegisterScaninaUserController extends RestApiController
{
    public function __invoke(string $xid, Request $request, Guard $userAuth, GuzzleUserRegisterService $service)
    {
        $requestBody = $this->validate($request, [
            'full_name' => 'required|string|max:255',
            'msisdn' => 'required|max:13|regex:/^[0-9]+$/',
            'city_id' => 'required|string',
            'password' => ['required', 'min:10', 'regex:/^(?=.*\d)(?=.*[a-zA-Z])/']
        ]);

        $registerRequestBody = new PostUserRegisterRequestDto(
            $requestBody + [
                'user_id' => $userAuth->id(),
                'xid' => $xid,
            ]
        );

        $result = $service->execute($registerRequestBody);

        return fractal($result, PostUserAccountResponseTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
