<?php

namespace Sanf\Core\Modules\PdcHold\Specifications;

use Sanf\Core\Modules\PdcHold\Models\PdcHoldGiroModel;

/**
 * @since CR2025
 */
final class EloquentPaginatePdcHoldSubmissionByUserAndProfileSpecification
{
    private int $userId;
    private string $customerId;
    private ?int $statusId;
    private ?int $type;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;

    public function __construct(int $userId, string $customerId, ?int $statusId, ?int $type, ?string $sortBy, ?int $skip, ?int $limit)
    {
        $this->userId = $userId;
        $this->customerId = $customerId;
        $this->statusId = $statusId;
        $this->type = $type;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
    }

    /**
     * @param PdcHoldGiroModel $model
     */
    public function build($model)
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
            ->withCount('giros')
            ->where('user_id', $this->userId)
            ->where('customer_id', $this->customerId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->statusId, function ($query) {
                return $query->where('status_id', $this->statusId);
            })
            ->when($this->type, function ($query) {
                return $query->where('type', $this->type);
            })
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })
            ->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });

        return $query;
    }
}
