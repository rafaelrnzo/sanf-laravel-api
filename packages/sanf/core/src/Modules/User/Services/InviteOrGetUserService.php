<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\EntityType;

class InviteOrGetUserService extends UserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->userRepository
            ->newQuery()
            ->where('username', $dto->email)
            ->first();

        if ($user) {
            if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
                $user->sendUserActivationNotification();
            }

            return $user;
        }

        /** @var \NbsPhp\Core\Models\AuthModel $user */
        $user = $this->userRepository
            ->newQuery()
            ->forceCreate([
                'full_name' => $dto->fullName,
                'username' => $dto->email,
                'password' => bcrypt(nano_id()),
                'status_id' => UserStatus::NEED_ACTIVATION,
                'entity_type_id' => EntityType::PERSONAL,
            ]);

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->sendUserActivationNotification();
        }

        return $user;
    }
}
