<?php


namespace Sanf\Core\Modules\Commodity\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityPublishedEvent;
use Sanf\Core\Modules\Commodity\GeneralCommodityException;

class PublishUserCommodityService extends CommodityService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->user_id != $user->id) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        if ($commodity->status_id !== CommodityStatus::UNPUBLISHED) {
            throw new GeneralCommodityException('Invalid State');
        }
        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::PUBLISHED,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityPublishedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
