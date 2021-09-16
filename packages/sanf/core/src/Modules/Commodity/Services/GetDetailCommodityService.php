<?php


namespace Sanf\Core\Modules\Commodity\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\GeneralCommodityException;

class GetDetailCommodityService extends CommodityService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->status_id != CommodityStatus::PUBLISHED) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $commodity->is_owner = ($commodity->user_id == $dto->userId);
        return $commodity;
        //TODO USER LOGGING USING EVENT
    }
}
