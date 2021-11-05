<?php


namespace Sanf\Core\Modules\Commodity\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\Dto\PaginateUserCommodityDto;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\Commodity\Specifications\CommoditySpecificationFactoryInterface;

class GetListCommodityService extends CommodityService implements ApplicationServiceInterface
{
    protected CommoditySpecificationFactoryInterface $specificationFactory;

    public function __construct(
        CommodityRepositoryInterface $commodityRepository,
        CommoditySpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($commodityRepository);
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param PaginateUserCommodityDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $data = $this->commodityRepository->query(
            $this->specificationFactory->paginate($dto->skip, $dto->limit, $dto->sortBy, $dto->timestamp, $dto->keyword)
        );
        $total = $this->commodityRepository->size(
            $this->specificationFactory->paginate($dto->skip, $dto->limit, $dto->sortBy, $dto->timestamp, $dto->keyword)
        );

        $data = collect($data)->map(function ($item) use ($dto) {
            $item->is_owner = ($item->user_id == $dto->userId);
            return $item;
        });

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
