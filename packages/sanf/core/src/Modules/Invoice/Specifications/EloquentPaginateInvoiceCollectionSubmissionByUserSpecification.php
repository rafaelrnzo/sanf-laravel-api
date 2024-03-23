<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Invoice\Models\InvoiceCollectionSubmissionModel;

final class EloquentPaginateInvoiceCollectionSubmissionByUserSpecification
{
    private int $userId;
    private string $profileXid;
    private ?string $keyword;
    private ?int $statusId;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;
    private ?int $timestamp;

    public function __construct(
        int $userId,
        string $profileXid,
        ?string $keyword,
        ?int $statusId,
        ?string $sortBy,
        ?int $skip,
        ?int $limit,
        ?int $timestamp
    ) {
        $this->userId = $userId;
        $this->profileXid = $profileXid;
        $this->keyword = $keyword;
        $this->statusId = $statusId;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
        $this->timestamp = $timestamp;
    }

    public function buildQuery(InvoiceCollectionSubmissionModel $model)
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
                return $query->where('name', 'ILIKE', '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('created_at_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });

        return $query;
    }
}
