<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Dtos\MyProfileDto;
use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class GetMyProfileService implements ApplicationServiceInterface
{
    protected $repository;

    protected $profileRepository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(UserRepositoryInterface $repository, ProfileRepositoryInterface $profileRepository) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
    }

    public function execute($dto = null)
    {
        /** @var \Sanf\Core\Modules\User\AuthEncryptedModel $user */
        $user = $this->repository->findById($dto->userId);

        if (is_null($user)) {
            throw new UserNotFoundException('User Not Found By Id');
        }

        if (empty($user->xid) || empty($user->personal_xid)) {
            $profile = $this->profileRepository->findPersonalProfileByEmail($user->username);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Email');
            }

            $this->repository->update([
                'xid' => $profile->getCustomerId(),
                'personal_xid' => $profile->getCustomerId(),
                'profile_type' => $profile->getTypeId(),
            ], $user->id);
        } else {
            $profile = $this->profileRepository->findById($user->personal_xid);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Id');
            }
        }

        $profile = $this->correctionIsPic($profile, $user);
        $result = $profile->toArray();
        $result['hasPassword'] = isset($user->password_updated_at) || isset($user->password);
        $result['hasPin'] = isset($user->pin);

        return new MyProfileDto($result);
    }

    protected function correctionIsPic(ProfileEntityInterface $profile, $user): ProfileEntityInterface
    {
        $isPic = $profile->getEmail() == $user->username && $profile->getIsPic();
        if ($isPic) {
            $profile->setAsPic();
        } else {
            $profile->setNotAsPic();
        }

        return $profile;
    }
}
