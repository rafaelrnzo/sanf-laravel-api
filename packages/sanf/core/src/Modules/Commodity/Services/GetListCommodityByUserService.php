<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\Dto\PaginateUserCommodityDto;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\Commodity\Specifications\CommoditySpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class GetListCommodityByUserService extends CommodityByUserService implements ApplicationServiceInterface
{
    protected CommoditySpecificationFactoryInterface $specificationFactory;

    public function __construct(
        CommodityRepositoryInterface $commodityRepository,
        AuthModel $userRepository,
        CommoditySpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($commodityRepository, $userRepository);
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param PaginateUserCommodityDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $this->findUserOrFail($dto->userId);

        $data = $this->commodityRepository->query(
            $this->specificationFactory->paginateByUser($dto->userId, $dto->skip, $dto->limit, $dto->sortBy, $dto->timestamp, $dto->keyword)
        );
        $total = $this->commodityRepository->size(
            $this->specificationFactory->paginateByUser($dto->userId, null, null, null, $dto->timestamp, $dto->keyword)
        );

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
