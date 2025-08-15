<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldReasonRequestDto;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldReasonModel;

/**
 * @since CR2025
 */
class PdcHoldReasonEloquentRepository extends AbstractEloquentRepository
{
    protected PdcHoldReasonModel $pdcHoldReasonModel;

    public function __construct(PdcHoldReasonModel $pdcHoldReasonModel)
    {
        $this->pdcHoldReasonModel = $pdcHoldReasonModel;
    }

    /**
     * @param BrowsePdcHoldReasonRequestDto $dto
     */
    public function list($dto): array
    {
        $query = $this->pdcHoldReasonModel
            ->newQuery()
            ->select([
                'id',
                'name',
                'has_free_text',
                'order',
            ])
            ->orderBy('order');

        $total = $query->count();

        $lists = $query
            ->limit($dto->limit)
            ->skip($dto->skip)
            ->get();

        return [
            'total' => $total,
            'count' => $lists->count(),
            'lists' => $lists,
        ];
    }
}
