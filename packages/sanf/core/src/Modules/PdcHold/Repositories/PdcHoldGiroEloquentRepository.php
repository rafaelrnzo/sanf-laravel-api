<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldGiroModel;

/**
 * @since CR2025
 */
class PdcHoldGiroEloquentRepository extends AbstractEloquentRepository implements PdcHoldGiroRepositoryInterface
{
    protected PdcHoldGiroModel $pdcHoldGiroModel;

    public function __construct(PdcHoldGiroModel $pdcHoldGiroModel)
    {
        $this->pdcHoldGiroModel = $pdcHoldGiroModel;
    }

    public function add(array $fields)
    {
        $model = $this->pdcHoldGiroModel->newQuery()->create($fields);

        return $this->stripEloquentModel($model);
    }

    public function findToResume(array $xids, string $customer_id)
    {
        return $this->pdcHoldGiroModel->newQuery()
            ->where('customer_id', $customer_id)
            ->whereIn('xid', $xids)
            ->with([
                'pdc_hold' => function ($query) {
                    $query->where('type', '!=', PdcHoldTypeEnum::RESUME);
                },
            ])
            ->whereHas('pdc_hold', function ($query) {
                $query
                    ->where('type', '!=', PdcHoldTypeEnum::RESUME)
                    ->where('status_id', PdcHoldStatusEnum::ACCEPTED);
            })
            ->get();
    }

    public function updateByXid(string $xid, array $fields): bool
    {
        return (bool) $this->pdcHoldGiroModel->newQuery()
            ->where('xid', $xid)
            ->update($fields);
    }
}
