<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\EntityType;
use Sanf\Integration\InternalApiClient;

class ActivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
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
        if (is_null($staff)) {
            throw new GeneralStaffException('Staff Not Found');
        }
        if (!filter_var($staff['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            throw new GeneralStaffException('Invalid Email Format');
        }

        $user = $this->userRepository->newQuery()->select('id')
            ->where('username', $staff['EMAIL'])->first();

        if (!$user) {
            /** @var \NbsPhp\Core\Models\AuthModel $user */
            $user = $this->userRepository->newQuery()->forceCreate([
                'full_name' => $staff['CUST_NAME'],
                'username' => $staff['EMAIL'],
                'password' => bcrypt(nano_id()),
                'status_id' => UserStatus::NEED_ACTIVATION,
                'entity_type_id' => EntityType::PERSONAL,
            ]);
        } elseif ($user->status_id == UserStatus::SUSPENDED) {
            $user->status_id = UserStatus::ACTIVE;
            $user->save();
        } else {
            throw new GeneralStaffException('Invalid Activation State');
        }

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->sendUserActivationNotification();
        }
    }
}
