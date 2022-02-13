<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Dtos\MyProfileDto;
use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class GetMyProfileService implements ApplicationServiceInterface
{
    protected $repository;

    protected $profileRepository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository, ProfileRepositoryInterface $profileRepository) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->findOrFail($dto->userId);
        if (empty($user->xid) || empty($user->personal_xid)) {
            $profile = $this->profileRepository->findPersonalProfileByEmail($user->username);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Email');
            }
            $user->xid = $profile->getCustomerId();
            $user->personal_xid = $profile->getCustomerId();
            $user->profile_type = $profile->getTypeId();
            $user->save();
        } else {
            $profile = $this->profileRepository->findById($user->personal_xid);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Id');
            }
        }

        $profile = $this->correctionIsPic($profile, $user);

        return new MyProfileDto($profile->toArray());
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
