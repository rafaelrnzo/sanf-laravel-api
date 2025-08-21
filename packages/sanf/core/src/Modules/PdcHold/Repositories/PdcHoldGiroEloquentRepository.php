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

    public function deletePastHolds(int $pdc_resume_id, string $customer_id, string $contract_no, string $pdc_no)
    {
        return $this->pdcHoldGiroModel->newQuery()
            ->where('customer_id', $customer_id)
            ->where('contract_no', $contract_no)
            ->where('pdc_no', $pdc_no)
            ->where(function ($query) use ($pdc_resume_id) {
                $query->whereNull('pdc_resume_id')
                    ->orWhere('pdc_resume_id', '<>', $pdc_resume_id);
            })
            ->delete();
    }

    public function deletePendingHolds(int $pdc_hold_id, string $customer_id, string $contract_no, string $pdc_no)
    {
        return $this->pdcHoldGiroModel->newQuery()
            ->where('pdc_hold_id', '<>', $pdc_hold_id)
            ->whereNull('pdc_resume_id')
            ->where('customer_id', $customer_id)
            ->where('contract_no', $contract_no)
            ->where('pdc_no', $pdc_no)
            ->delete();
    }
}
