<?php

namespace Sanf\Core\Modules\User\Services;

use function bcrypt;
use function collect;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\EmailAlreadyExistException;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\ContractOwnerNotFoundException;
use Sanf\Core\Modules\User\Enums\EntityType;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class RegisterAsContractOwnerService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    /**
     * VerifyEmailService constructor.
     * @param $repository
     */
    public function __construct(UserRepositoryInterface $repository, SanfCoreApiClient $internalApiClient) //TODO USE REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $customer = $this->internalApiClient->findCustomerByEmail($dto->email);
        $data = collect($customer['data']);
        $personalData = $data->firstWhere('ID_IDENTITY', 'P');
        if (!$personalData) {
            throw new ContractOwnerNotFoundException();
        }

        $emailExists = SodiumEncryption::query()->transaction(function () use ($dto) {
            return $this->repository->existsByEmail($dto->email);
        });

        if ($emailExists) {
            throw new EmailAlreadyExistException();
        }

        /** @var AuthModel $user */
        $user = $this->repository->create([
            'entity_type_id' => EntityType::PERSONAL,
            'username' => $personalData['EMAIL_ADDR'],
            'password' => bcrypt(nano_id()), // set temporary random password
            'full_name' => $personalData['IDENTITY_NAME'],
            'landline_number' => $personalData['NO_TELP'],
            'phone_number' => $personalData['NO_HP'],
            'status_id' => UserStatus::NEED_ACTIVATION,
            'xid' => $personalData['CUST_ID_SANF'],
            'profile_type' => $personalData['ID_IDENTITY'],
        ]);

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->sendUserActivationNotification();
        }
    }
}
