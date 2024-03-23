<?php

namespace Sanf\Core\Modules\Project\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Dto\PaginateUserProjectDto;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\Project\Specifications\ProjectSpecificationFactoryInterface;

class GetListProjectService extends ProjectService implements ApplicationServiceInterface
{
    protected ProjectSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($projectRepository);
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param PaginateUserProjectDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $data = $this->projectRepository->query(
            $this->specificationFactory->paginate($dto->skip, $dto->limit, $dto->sortBy, $dto->timestamp, $dto->keyword)
        );
        $total = $this->projectRepository->size(
            $this->specificationFactory->paginate(null, null, null, $dto->timestamp, $dto->keyword)
        );

        $data = collect($data)->map(function ($item) use ($dto) {
            $item->is_owner = ($item->user_id == $dto->userId);

            return $item;
        });

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => (int) $total,
                'count' => collect($data)->count(),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
