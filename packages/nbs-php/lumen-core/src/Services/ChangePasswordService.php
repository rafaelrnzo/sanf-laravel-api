<?php


namespace NbsPhp\Core\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;

class ChangePasswordService implements ApplicationServiceInterface
{
    public function execute($dto = null): bool
    {
        $user = AuthModel::find($dto->userId);
        if(!$user){
            throw new UserNotFoundException();
        }
        if(!Hash::check($dto->currentPassword, $user->password)){
            throw new InvalidCredentialException();
        }
        $user->password = bcrypt($dto->newPassword);
        $user->password_updated_at = Carbon::now();
        return $user->save();
    }
}
