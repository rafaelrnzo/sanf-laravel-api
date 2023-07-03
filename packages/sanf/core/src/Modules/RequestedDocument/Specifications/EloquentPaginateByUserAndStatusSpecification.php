<?php

namespace Sanf\Core\Modules\RequestedDocument\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\RequestedDocument\Models\RequestedDocumentModel;

class EloquentPaginateByUserAndStatusSpecification
{
    private string $userId;
    private ?int $statusId;
    private ?int $typeId;
    private ?string $keyword;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;
    private ?int $timestamp;

    /**
     * @param string $userId
     * @param int|null $statusId
     * @param int|null $typeId
     * @param string|null $keyword
     * @param string|null $sortBy
     * @param int|null $skip
     * @param int|null $limit
     * @param int|null $timestamp
     */
    public function __construct(
        string $userId,
        ?int $statusId,
        ?int $typeId,
        ?string $keyword,
        ?string $sortBy,
        ?int $skip,
        ?int $limit,
        ?int $timestamp
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->typeId = $typeId;
        $this->keyword = $keyword;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->timestamp = $timestamp;
    }

    public function buildQuery(RequestedDocumentModel $model)
    {
        switch ($this->sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'request_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'request_at';
                $orderDirection = 'DESC';
        }

        return $model->newQuery()
            ->select([
                'id',
                'xid',
                'user_id',
                'profile_xid',
                'request_no',
                'request_at',
                'document_no',
                'type',
                'total_item',
                'total_uploaded',
                'status',
            ])
            ->with([
                'items' => function ($query) {
                    return $query->whereNull('deleted_at');
                }
            ])
            ->whereNull('deleted_at')
            ->where('user_id', '=', $this->userId)
            ->when($this->statusId, function ($query) {
                return $query->where('status', '=', $this->statusId);
            })
            ->when($this->typeId, function ($query) {
                return $query->where('type', '=', $this->typeId);
            })
            ->when($this->keyword, function ($query) {
                return $query->where(function ($q) {
                    return $q->where('request_no', 'ilike', "%{$this->keyword}%")
                        ->orWhere('document_no', 'ilike', "%{$this->keyword}%");
                });
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('request_at', '>', Carbon::createFromTimestamp($this->timestamp));
            })
            ->orderBy($orderBy, $orderDirection);
    }
}
