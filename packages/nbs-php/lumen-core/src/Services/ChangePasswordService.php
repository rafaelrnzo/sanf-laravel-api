<?php


namespace NbsPhp\Core\Services;


use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;

class ChangePasswordService implements ApplicationServiceInterface
{
    public function execute($dto): bool
    {
        $user = AuthModel::find($dto->userId);
        if(!$user){
            throw new UserNotFoundException();
        }
        if(!Hash::check($dto->currentPassword, $user->password)){
            throw new InvalidCredentialException();
        }
        $user->password = bcrypt($dto->newPassword);
        return $user->save();
    }
}
