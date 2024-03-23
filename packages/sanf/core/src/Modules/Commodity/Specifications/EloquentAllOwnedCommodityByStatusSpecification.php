<?php

namespace Sanf\Core\Modules\Commodity\Specifications;

use Sanf\Core\Modules\Commodity\Models\CommodityModel;

class EloquentAllOwnedCommodityByStatusSpecification
{
    private int $userId;
    private array $statuses;

    /**
     * EloquentAllOwnedCommodityByStatusSpecification constructor.
     * @param int $userId
     * @param array $status
     */
    public function __construct(int $userId, array $statuses = [])
    {
        $this->userId = $userId;
        $this->statuses = $statuses;
    }

    public function buildQuery(CommodityModel $model)
    {
        return $model->newQuery()
            ->where('user_id', $this->userId)
            ->whereIn('status_id', $this->statuses);
    }
}
