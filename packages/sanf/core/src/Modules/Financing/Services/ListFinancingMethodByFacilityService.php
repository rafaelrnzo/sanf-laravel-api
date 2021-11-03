<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodResultDto;
use Sanf\Core\Modules\Financing\Exceptions\FinancingGeneralException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingMethodByFacilitySpecificationFactoryInterface;

class ListFinancingMethodByFacilityService extends FinancingService implements ApplicationServiceInterface
{
    protected FinancingMethodByFacilitySpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingFacilityRepositoryInterface $financingFacilityRepository,
        FinancingMethodByFacilitySpecificationFactoryInterface $specificationFactory
    )
    {
        parent::__construct(
            $financingApplicationRepository,
            $financingMethodRepository,
            $financingPrerequisiteRepository,
            $financingFacilityRepository
        );
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

        // Get data from specification factory
        $data = $this->financingFacilityRepository->first(
            $this->specificationFactory->paginate($dto->id, $dto->skip, $dto->limit , $dto->sort_by)
        )->methods;

        if(is_null($data)){
            throw new FinancingGeneralException('Financing Facility Not Found');
        }

        $total = count($data) + $dto->skip;

        $paginate = (object)[
            'total' => (int)$total,
            'count' => count($data),
            'skip' => (int) $dto->skip,
            'limit' => (int)$dto->limit,
            'sort_by' => $dto->sort_by,
        ];

        // sent list data;
        return new ListFinancingMethodResultDto([
            'data' => $data,
            'paginate'=> $paginate
        ]);
    }
}
