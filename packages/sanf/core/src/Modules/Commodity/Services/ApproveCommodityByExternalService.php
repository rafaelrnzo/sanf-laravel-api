<?php


namespace Sanf\Core\Modules\Commodity\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityApprovedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;

class ApproveCommodityByExternalService extends CommodityService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity)) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $updatedCommodity =  $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::PUBLISHED,
            'published_at' => Carbon::now(),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityApprovedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
