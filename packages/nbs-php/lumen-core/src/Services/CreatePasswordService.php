<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;

class CreatePasswordService implements ApplicationServiceInterface
{
    public function execute($dto = null): bool
    {
        $user = AuthModel::find($dto->userId);
        if(!$user){
            throw new UserNotFoundException();
        }
        $user->password = bcrypt($dto->password);
        $user->password_updated_at = Carbon::now();

        return $user->save();
    }
}
