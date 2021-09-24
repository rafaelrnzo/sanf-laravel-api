<?php


namespace Sanf\Core\Modules\Commodity\Services;


use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;

class CommodityService
{
    protected CommodityRepositoryInterface $commodityRepository;

    public function __construct(CommodityRepositoryInterface $commodityRepository)
    {
        $this->commodityRepository = $commodityRepository;
    }
}
