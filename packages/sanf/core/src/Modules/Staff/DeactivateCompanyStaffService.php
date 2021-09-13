<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class DeactivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected InternalApiClient $internalApiClient;

    /**
     * ActivateCompanyStaffService constructor.
     * @param AuthModel $userRepository
     */
    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto)
    {
        $response = $this->internalApiClient->getStaffs($dto->xid);
        $staff = collect($response['data'])->firstWhere('SR_NO', $dto->no);
        if(is_null($staff)){
            throw new GeneralStaffException('Staff Not Found');
        }
        if (!filter_var($staff['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            throw new GeneralStaffException('Invalid Email Format');
        }

        $user = $this->userRepository->newQuery()->where('username', $staff['EMAIL'])->first();
        if (is_null($user)){
            throw new UserNotFoundException();
        }

        $user->status_id = UserStatus::SUSPENDED;
        $user->save();
    }
}
