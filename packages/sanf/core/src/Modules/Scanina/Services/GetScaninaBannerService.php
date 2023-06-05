<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Promo\PromoModel;
use Sanf\Core\Modules\Promo\PromoRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaRegionRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaRegionSpecificationInterface;

class GetScaninaBannerService implements ApplicationServiceInterface
{

    private PromoRepositoryInterface $repository;

    public function __construct(
        PromoRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $banner = $this->repository->findByXid(PromoModel::SANF_SCANINA);
        if (!$banner) {
            throw new ModelNotFoundException();
        }

        return $banner;
    }
}
