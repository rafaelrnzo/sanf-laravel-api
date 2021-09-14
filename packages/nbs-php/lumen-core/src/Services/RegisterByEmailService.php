<?php


namespace NbsPhp\Core\Services;


use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\EmailAlreadyExistException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\AuthModel;

class RegisterByEmailService implements RegisterByEmailServiceInterface
{
    protected $jwt;

    protected $repository;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    public function __construct(JWTHelper $jwt, AuthModel $repository) //TODO USE REPOSITORY
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        if ($this->repository->newQuery()->select('id')->where('username', $dto->email)->first()) {
            throw new EmailAlreadyExistException();
        }

        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->forceCreate([
            'full_name' => $dto->fullName,
            'username' => $dto->email,
            'landline_number' => $dto->landlineNumber,
            'phone_number' => $dto->phoneNumber,
            'password' => bcrypt($dto->password),
            'password_updated_at' => Carbon::now(),
            'status_id' => UserStatus::INACTIVE,
            'entity_type_id' => EntityType::ADMIN, //TODO CONFIGURABLE
        ]);

        if ($user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
