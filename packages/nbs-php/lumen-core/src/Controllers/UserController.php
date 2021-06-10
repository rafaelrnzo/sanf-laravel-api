<?php


namespace NbsPhp\Core\Controllers;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Services\GetProfileService;
use NbsPhp\Core\Services\UpdateProfileService;
use NbsPhp\Core\Transformers\ProfileTransformer;

class UserController extends RestController
{
    public function getProfile(Guard $auth, GetProfileService $service)
    {
        $dto = (object)['userId' => $auth->id()];

        $data = $service->execute($dto);

        return $this->responseOk('Success', fractal($data, config('auth.profile_transformer')));
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
