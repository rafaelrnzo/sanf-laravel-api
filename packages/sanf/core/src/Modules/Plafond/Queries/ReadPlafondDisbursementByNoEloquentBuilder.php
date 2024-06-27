<?php

namespace Sanf\Core\Modules\Plafond\Queries;

use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;

class ReadPlafondDisbursementByNoEloquentBuilder
{
    private ReadPlafondDisbursementRequestDto $dto;

    /**
     * @param ReadPlafondDisbursementRequestDto $dto
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
        /** @var ReadPlafondDisbursementRequestDto $dto */
        $dto = $this->dto;

        return $plafondDisbursementModel->newQuery()
            ->with([
                'disbursementRelation',
                'disbursementRelation.invoicesRelation',
                'disbursementRelation.invoicesRelation.photosRelation',
                'disbursementRelation.allocationsRelation',
                'disbursementRelation.documentsRelation',
            ])
            ->where('disbursement_no', '=', $dto->disbursementXid)
            ->where('plafond_id', '=', $dto->plafondXid)
            ->where('client_id', '=', $dto->profileXid)
            ->when($dto->limit, function ($query) use ($dto) {
                return $query->limit($dto->limit);
            });
    }
}
