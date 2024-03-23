<?php

namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingApplicationModel;

class EloquentPaginateFinancingApplicationByUserSpecification
{
    private int $userId;
    private string $profileXid;
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;
    private ?string $keyword;

    public function __construct(int $userId, string $profileXid, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        $this->userId = $userId;
        $this->profileXid = $profileXid;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
        $this->keyword = $keyword;
    }

    public function buildQuery(FinancingApplicationModel $model)
    {
        switch ($this->sortBy) {
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
        $query = $model->newQuery()
            ->with(['status', 'facility', 'method', 'objects'])
            ->where('user_id', $this->userId)
            ->where('profile_xid', $this->profileXid)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->keyword, function ($query) {
                return $query->where('name', 'ILIKE', '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });

        return $query;
    }
}
