<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\ProjectStatus;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\Project\Specifications\ProjectSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class GetUserProjectMetadataService extends ProjectService implements ApplicationServiceInterface
{
    protected ProjectSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        AuthModel $userRepository,
        ProjectSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($projectRepository, $userRepository);
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        $this->findUserOrFail($dto->userId);
        $publishedCount = $this->projectRepository->size(
            $this->specificationFactory->getAllOwnedByStatus($dto->userId, [ProjectStatus::PUBLISHED])
        );
        $totalCount = $this->projectRepository->size(
            $this->specificationFactory->getAllOwned($dto->userId)
        );

        return (object)[
            'publishedCount' => $publishedCount,
            'totalCount' => $totalCount
        ];
    }
}
