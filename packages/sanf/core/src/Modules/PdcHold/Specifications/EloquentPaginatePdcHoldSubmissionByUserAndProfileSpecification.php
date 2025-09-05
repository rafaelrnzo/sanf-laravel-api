<?php

namespace Sanf\Core\Modules\PdcHold\Specifications;

use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldGiroModel;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldModel;

/**
 * @since CR2025
 */
final class EloquentPaginatePdcHoldSubmissionByUserAndProfileSpecification
{
    private int $userId;
    private string $customerId;
    private ?int $statusId;
    private ?int $type;
    private ?bool $resumable;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;

    public function __construct(int $userId, string $customerId, ?int $statusId, ?int $type, ?bool $resumable, ?string $sortBy, ?int $skip, ?int $limit)
    {
        $this->userId = $userId;
        $this->customerId = $customerId;
        $this->statusId = $statusId;
        $this->type = $type;
        $this->resumable = $resumable;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
    }

    /**
     * @param PdcHoldModel $model
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
            ->addSelect([
                'contracts_hold_count' => PdcHoldGiroModel::query()
                    ->selectRaw('COUNT(DISTINCT "contract_no")')
                    ->whereColumn('pdc_hold_giros.pdc_hold_id', $model->getQualifiedKeyName())
                    ->limit(1),
            ])
            ->addSelect([
                'contracts_resume_count' => PdcHoldGiroModel::query()
                    ->selectRaw('COUNT(DISTINCT "contract_no")')
                    ->whereColumn('pdc_hold_giros.pdc_resume_id', $model->getQualifiedKeyName())
                    ->limit(1),
            ])
            ->withCount(['giros_no_resume', 'resume_giros'])
            ->where('user_id', $this->userId)
            ->where('customer_id', $this->customerId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->resumable, function ($query) {
                return $query->where('status_id', PdcHoldStatusEnum::ACCEPTED)
                    ->whereIn('type', PdcHoldTypeEnum::RESUMABLE);
            })
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
