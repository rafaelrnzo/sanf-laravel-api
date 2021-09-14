<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Models\AuthModel;

class GetProfileService implements ApplicationServiceInterface
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
        return json_decode(json_encode(AuthModel::findOrFail($dto->userId)));
    }
}
