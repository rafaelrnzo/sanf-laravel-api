<?php

namespace Sanf\Core\Modules\Insurance\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Insurance\Models\InsuranceClaimSubmissionModel;

final class EloquentPaginateInsuranceClaimSubmissionByUserAndProfileSpecification
{
    private int $userId;
    private string $profileXid;
    private ?string $keyword;
    private ?int $statusId;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;
    private ?int $timestamp;

    /**
     * EloquentPaginateInsuranceClaimSubmissionByUserAndProfileSpecification constructor.
     * @param int $userId
     * @param string $profileXid
     * @param string|null $keyword
     * @param int|null $statusId
     * @param string|null $sortBy
     * @param int|null $skip
     * @param int|null $limit
     * @param int|null $timestamp
     */
    public function __construct(int $userId, string $profileXid, ?string $keyword, ?int $statusId, ?string $sortBy, ?int $skip, ?int $limit, ?int $timestamp)
    {
        $this->userId = $userId;
        $this->profileXid = $profileXid;
        $this->keyword = $keyword;
        $this->statusId = $statusId;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->timestamp = $timestamp;
    }

    public function buildQuery(InsuranceClaimSubmissionModel $model)
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
            ->with('status')
            ->where('user_id', $this->userId)
            ->where('profile_xid', $this->profileXid)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->statusId, function ($query) {
                return $query->where('status_id', $this->statusId);
            })->when($this->keyword, function ($query) {
                return $query->where('contract_no', 'ILIKE', '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });

        return $query;
    }
}
