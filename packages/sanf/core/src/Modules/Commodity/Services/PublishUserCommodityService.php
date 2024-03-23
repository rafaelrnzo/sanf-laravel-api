<?php

namespace Sanf\Core\Modules\Commodity\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityPublishedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;
use Sanf\Core\Modules\Commodity\Exceptions\InvalidStateCommodityException;

class PublishUserCommodityService extends CommodityByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->user_id != $user->id) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        if ($commodity->status_id !== CommodityStatus::UNPUBLISHED) {
            throw new InvalidStateCommodityException('Invalid State');
        }
        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::PUBLISHED,
            'published_at' => Carbon::now(),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityPublishedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
