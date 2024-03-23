<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\Events\CommodityDeletedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;

class DeleteCommodityByService extends CommodityByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->user_id != $user->id) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $result = $this->commodityRepository->removeById($commodity->id);

        event(new CommodityDeletedEvent($commodity));

        return $result;
    }
}
