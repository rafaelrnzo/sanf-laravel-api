<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\ListFinancingPrerequisiteResultDto;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingSpecificationFactoryInterface;

class ListFinancingPrerequisiteService extends FinancingService implements ApplicationServiceInterface
{
    protected FinancingSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($financingMethodRepository,$financingPrerequisiteRepository);
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        switch ($dto->sort_by) {
            case 'latest':
                $dto->sort_by = 'DESC';
                break;
            case 'earliest':
                $dto->sort_by = 'ASC';
                break;
            default:
                $dto->sort_by = 'ASC';
        }

        // Get data from specification factory financing prerequisite
        $data = $this->financingPrerequisiteRepository->query(
            $this->specificationFactory->paginateFinancingPrerequisite($dto->skip, $dto->limit , $dto->sort_by)
        );

        $total = $this->financingPrerequisiteRepository->size(
            $this->specificationFactory->paginateFinancingPrerequisite($dto->skip, $dto->limit , $dto->sort_by)
        );

        // Assert paginate to object
        $paginate = (object)[
            'total' => (int)$total,
            'count' => count($data),
            'skip' => (int) $dto->skip,
            'limit' => (int)$dto->limit,
            'sort_by' => $dto->sort_by,
        ];

        // sent list data;
        return new ListFinancingPrerequisiteResultDto([
            'data' => $data,
            'paginate'=> $paginate
        ]);
    }
}
