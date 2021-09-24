<?php


namespace Sanf\Core\Modules\Commodity\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;

class RejectCommodityByExternalService extends CommodityService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity)) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::REJECTED,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityUpdatedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
