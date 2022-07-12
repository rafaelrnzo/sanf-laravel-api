<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use Carbon\Carbon;
use Sanf\Core\Modules\Contract\Models\ESignDocumentModel;

class EloquentPaginateByUserIdSpecification
{
    private int $userId;
    private ?int $statusId;
    private ?string $keyword;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;
    private ?int $timestamp;

    /**
     * @param int $userId
     * @param int|null $statusId
     * @param string|null $keyword
     * @param string|null $sortBy
     * @param int|null $skip
     * @param int|null $limit
     * @param int|null $timestamp
     */
    public function __construct(
        int $userId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->keyword = $keyword;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->timestamp = $timestamp;
    }

    public function buildQuery(ESignDocumentModel $model)
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

        return $model->newQuery()
            ->where('expired_at', '>', Carbon::now())
            ->where('user_id', '=', $this->userId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->statusId, function ($query) {
                return $query->where('status_id', $this->statusId);
            })->when($this->keyword, function ($query) {
                return $query->where('document_name', "ILIKE", '%' . $this->keyword . '%')
                    ->orWhere('document_id', "ILIKE", '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });
    }
}