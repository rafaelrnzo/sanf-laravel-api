<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\EmailAlreadyExistException;
use NbsPhp\Core\Jwt\JWTHelper;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class RegisterByEmailService implements RegisterByEmailServiceInterface
{
    protected $jwt;

    protected $repository;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    public function __construct(JWTHelper $jwt, UserRepositoryInterface $repository) //TODO USE REPOSITORY
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $hasExist = SodiumEncryption::query()->transaction(
            function () use ($dto) {
                return $this->repository->existsByEmailAndStatusIds(
                    $dto->email,
                    [UserStatus::ACTIVE, UserStatus::NEED_ACTIVATION]
                );
            }
        );

        if ($hasExist) {
            throw new EmailAlreadyExistException();
        }

        /** @var AuthEncryptedModel $user */
        $user = $this->repository->forceCreate([
            'full_name' => $dto->fullName,
            'username' => $dto->email,
            'landline_number' => $dto->landlineNumber,
            'phone_number' => $dto->phoneNumber,
            'password' => bcrypt($dto->password),
            'password_updated_at' => (string) Carbon::now(),
            'status_id' => UserStatus::ACTIVE,
            'entity_type_id' => EntityType::ADMIN, //TODO CONFIGURABLE
        ]);

        if ($user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
