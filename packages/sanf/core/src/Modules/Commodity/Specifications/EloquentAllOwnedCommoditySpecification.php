<?php

namespace Sanf\Core\Modules\Commodity\Specifications;

use Sanf\Core\Modules\Commodity\Models\CommodityModel;

class EloquentAllOwnedCommoditySpecification
{
    private int $userId;

    /**
     * EloquentUserCommodityMetadataSpecification constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function buildQuery(CommodityModel $model)
    {
        return $model->newQuery()
            ->where('user_id', $this->userId);
    }
}
