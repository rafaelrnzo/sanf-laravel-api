<?php

namespace Sanf\Core\Modules\PdcHold\Queries;

use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldRequestDto;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldModel;

/**
 * @since CR2025
 */
class BrowsePdcHoldEloquentBuilder
{
    protected BrowsePdcHoldRequestDto $dto;

    public function __construct(BrowsePdcHoldRequestDto $dto)
    {
        $this->dto = $dto;
    }

    public function build(PdcHoldModel $pdcHoldModel)
    {
        /** @var BrowsePdcHoldRequestDto $dto */
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

        return $pdcHoldModel->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->withCount('giros')
            ->where('customer_id', '=', $dto->profileXid)
            ->when($dto->statusId, function ($query) use ($dto) {
                return $query->whereIn('status_id', $dto->statusId);
            })
            ->when($dto->skip, function ($query) use ($dto) {
                return $query->skip($dto->skip);
            })
            ->when($dto->limit, function ($query) use ($dto) {
                return $query->limit($dto->limit);
            });
    }
}
