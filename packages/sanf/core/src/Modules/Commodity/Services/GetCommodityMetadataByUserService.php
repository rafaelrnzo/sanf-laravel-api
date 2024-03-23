<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\Commodity\Specifications\CommoditySpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class GetCommodityMetadataByUserService extends CommodityByUserService implements ApplicationServiceInterface
{
    protected CommoditySpecificationFactoryInterface $specificationFactory;

    public function __construct(
        CommodityRepositoryInterface           $commodityRepository,
        AuthModel                              $userRepository,
        CommoditySpecificationFactoryInterface $specificationFactory
    )
    {
        parent::__construct($commodityRepository, $userRepository);
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        $publishedCount = $this->commodityRepository->size(
            $this->specificationFactory->getAllOwnedByStatus($user->id, [CommodityStatus::PUBLISHED])
        );
        $totalCount = $this->commodityRepository->size(
            $this->specificationFactory->getAllOwned($user->id)
        );

        return (object) [
            'publishedCount' => $publishedCount,
            'totalCount' => $totalCount,
        ];
    }
}
