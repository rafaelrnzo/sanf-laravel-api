<?php

namespace Sanf\Core\Modules\Plafond\Queries;

use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;

class BrowsePlafondDisbursementEloquentBuilder
{
    private BrowsePlafondDisbursementRequestDto $dto;

    /**
     * @param BrowsePlafondDisbursementRequestDto $dto
     * @return mixed|Illuminate\Database\Eloquent\Builder
     */
    public function __construct($dto)
    {
        $this->dto = $dto;
    }

    /**
     * @param PlafondDisbursementModel $plafondDisbursementModel
     * @return mixed|Illuminate\Database\Eloquent\Builder
     */
    public function build(PlafondDisbursementModel $plafondDisbursementModel)
    {
        /** @var BrowsePlafondDisbursementRequestDto $dto */
        $dto = $this->dto;
        switch ($dto->sortBy) {
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

        $statusCondition = null;
        if (in_array($dto->statusId, PlafondDisbursementStatusEnum::SUBMIT_TAB)) {
            $statusCondition = PlafondDisbursementStatusEnum::SUBMIT_TAB;
        }
        if (in_array($dto->statusId, PlafondDisbursementStatusEnum::PROCESS_TAB)) {
            $statusCondition = PlafondDisbursementStatusEnum::PROCESS_TAB;
        }
        if (in_array($dto->statusId, PlafondDisbursementStatusEnum::DONE_TAB)) {
            $statusCondition = PlafondDisbursementStatusEnum::DONE_TAB;
        }

        return $plafondDisbursementModel->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->with(['disbursementRelation' => function ($query) {
                return $query->select([
                    'id',
                    'xid',
                    'revision_notes',
                    'created_at',
                    'updated_at',
                ]);
            }])
            ->where('plafond_id', '=', $dto->plafondXid)
            ->where('client_id', '=', $dto->profileXid)
            ->when($statusCondition, function ($query) use ($statusCondition) {
                return $query->whereIn('status_id', $statusCondition);
            })
            ->when($dto->keyword, function ($query) use ($dto) {
                return $query->where(function ($subQuery) use ($dto) {
                    return $subQuery->where('disbursement_no', 'ilike', "%{$dto->keyword}%")
                        ->orWhere('client_mail', 'ilike', "%{$dto->keyword}%")
                        ->orWhere('customer_mail', 'ilike', "%{$dto->keyword}%");
                });
            })
            ->when($dto->skip, function ($query) use ($dto) {
                return $query->skip($dto->skip);
            })
            ->when($dto->limit, function ($query) use ($dto) {
                return $query->limit($dto->limit);
            });
    }
}
