<?php

namespace Sanf\Core\Modules\Disbursement\Repositories;

use Illuminate\Support\Collection;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;

class SparePartDisbursementEloquentRepository extends AbstractEloquentRepository implements SparePartDisbursementRepositoryInterface
{
    protected SparePartDisbursementModel $disbursementModel;

    public function __construct(
        SparePartDisbursementModel $disbursementModel
    ) {
        $this->disbursementModel = $disbursementModel;
    }

    public function listQuery(object $params)
    {
        $plafondXid = $params->plafondXid;
        $statusIds = $params->statusIds ?? [];
        $statusId = $params->statusId;
        $keyword = $params->keyword;

        return $this->disbursementModel->newQuery()
            ->where('customer_id', '=', $params->profileXid)
            ->when($plafondXid, function ($query, $plafondXid) {
                $query->where('plafond_no', $plafondXid);
            })
            ->when(!empty($statusIds), function ($query) use ($statusIds) {
                return $query->whereIn('status_id', $statusIds);
            })
            ->when($statusId, function ($query, $statusId) {
                return $query->where('status_id', $statusId);
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('disbursement_no', 'ilike', "%{$keyword}%");
            });
    }

    /**
     * @param object $params
     * @return Collection<SparePartDisbursementModel>
     */
    public function list(object $params): Collection
    {
        $sortBy = $params->sortBy;
        $skip = $params->skip;
        $limit = $params->limit;

        switch ($sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }

        return $this->listQuery($params)
            ->with(['supplier'])
            ->when($skip, function ($query, $skip) {
                return $query->skip($skip);
            })
            ->when($limit, function ($query, $limit) {
                return $query->limit($limit);
            })
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function listCount(object $params): int
    {
        return $this->listQuery($params)->count();
    }
}
