<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Dto\PaginateUserProjectDto;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\Project\Specifications\ProjectSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class GetListProjectByUserService extends ProjectByUserService implements ApplicationServiceInterface
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

    /**
     * @param PaginateUserProjectDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $this->findUserOrFail($dto->userId);

        $spesification = $this->specificationFactory->paginateByUser($dto->userId, $dto->skip, $dto->limit, $dto->sortBy, $dto->timestamp, $dto->keyword);

        $data = $this->projectRepository->query($spesification);
        $total = $this->projectRepository->size($spesification);

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => (int)$total,
                'count' => collect($data)->count(),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
