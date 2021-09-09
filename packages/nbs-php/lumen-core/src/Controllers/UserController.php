<?php


namespace NbsPhp\Core\Controllers;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Services\GetProfileService;
use NbsPhp\Core\Services\UpdateProfileService;

class UserController extends RestApiController
{
    public function getProfile(Guard $auth, GetProfileService $service)
    {
        $dto = (object)['userId' => $auth->id()];

        $data = $service->execute($dto);

        return $this->responseOk('Success', fractal($data, config('auth.transformers.profile')));
    }

    public function updateProfile(Request $request, Guard $auth, UpdateProfileService $service)
    {
        $input = $this->validate($request, [
            'full_name' => ['string', 'nullable']
        ]);
        $dto = (object)[
            'userId' => $auth->id(),
            'fullName' => $input['full_name']];

        $service->execute($dto);

        return $this->responseOk();
    }
}
