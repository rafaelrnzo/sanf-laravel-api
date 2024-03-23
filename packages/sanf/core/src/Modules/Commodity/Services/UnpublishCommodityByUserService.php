<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityUnpublishedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;

class UnpublishCommodityByUserService extends CommodityByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->user_id != $user->id) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::UNPUBLISHED,
            'published_at' => null,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityUnpublishedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
