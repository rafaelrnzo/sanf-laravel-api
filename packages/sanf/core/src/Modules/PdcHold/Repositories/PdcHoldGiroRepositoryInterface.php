<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

use Illuminate\Support\Collection;

/**
 * @since CR2025
 */
interface PdcHoldGiroRepositoryInterface
{
    public function add(array $fields);

    public function findToResume(array $xids, string $customer_id);

    public function updateByXid(string $xid, array $fields): bool;

    public function deletePastHolds(int $pdc_resume_id, string $customer_id, string $contract_no, string $pdc_no);

    public function deletePendingHolds(int $pdc_hold_id, string $customer_id, string $contract_no, string $pdc_no);

    public function getSubmitted(array $fileds, array $filters): Collection;
}
