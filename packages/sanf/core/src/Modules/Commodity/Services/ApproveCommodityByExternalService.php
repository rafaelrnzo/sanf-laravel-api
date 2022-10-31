<?php

namespace Sanf\Core\Modules\Commodity\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityApprovedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ApproveCommodityByExternalService extends CommodityService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(CommodityRepositoryInterface $commodityRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($commodityRepository);
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity)) {
            throw new GeneralCommodityException('Commodity Not Found');
        }
        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'status_id' => CommodityStatus::PUBLISHED,
            'published_at' => Carbon::now(),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        $user = $this->userRepository->findById($commodity->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        event(new CommodityApprovedEvent($commodity, $updatedCommodity, $user));

        return $updatedCommodity;
    }
}
