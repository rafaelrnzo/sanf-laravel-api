<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Models\AuthModel;

class UpdateProfileService implements ApplicationServiceInterface
{
    protected $repository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository) //TODO REPOSITORY
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->findOrFail($dto->userId)
            ->forceFill([
                'full_name' => $dto->fullName
            ])->save();
        return json_decode(json_encode($user));
    }
}
