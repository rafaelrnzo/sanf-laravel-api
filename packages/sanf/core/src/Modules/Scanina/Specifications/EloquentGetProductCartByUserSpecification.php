<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Scanina\Models\ScaninaProductCartModel;

class EloquentGetProductCartByUserSpecification
{
    private object $parameter;

    /**
     * @param object $parameter
     */
    public function __construct(object $parameter)
    {
        $this->parameter = $parameter;
    }

    public function buildQuery(ScaninaProductCartModel $model)
    {
        $keyword = $this->parameter->keyword ?? null;
        $skip = $this->parameter->skip ?? null;
        $limit = $this->parameter->limit ?? null;
        $timestamp = $this->parameter->timestamp ?? null;
        $sortBy = $this->parameter->sortBy ?? null;

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

        return $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->where('profile_xid', '=', $this->parameter->profileXid)
            ->where('type_id', '=', $this->parameter->productType)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('snapshot_response_body->name', 'ILIKE', '%' . $keyword . '%');
            })->when($skip, function ($query) use ($skip) {
                return $query->skip($skip);
            })->when($limit, function ($query) use ($limit) {
                return $query->limit($limit);
            })->when($timestamp, function ($query) use ($timestamp) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($timestamp));
            });
    }
}
