<?php

namespace Sanf\Core\Modules\Disbursement\Repositories;

use Illuminate\Support\Collection;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementBatchModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;

interface SparePartDisbursementRepositoryInterface
{
    /**
     * @param object $params
     * @return Collection<SparePartDisbursementModel>
     */
    public function list(object $params): Collection;

    public function listCount(object $params): int;

    public function find(array $filters): ?SparePartDisbursementModel;

    public function update(array $filters, array $data): bool;

    /**
     * @param array $select
     * @param array $filters
     * @return Collection<SparePartDisbursementModel>
     */
    public function get(array $select = ['*'], array $filters = [], array $relations = []): Collection;

    public function listInvoice(array $filters): Collection;

    public function findInvoice(array $filters): ?SparePartDisbursementInvoiceModel;

    public function rejectInvoices(array $filters, array $invoiceXids): int;

    public function approveInvoicesWithExclusion(array $filters, array $excludeInvoiceXids): int;

    public function updateInvoice(array $filters, array $data): bool;

    public function findBatch(array $filters): ?SparePartDisbursementBatchModel;

    public function listUploadedDocument(array $filters): Collection;

    public function updateBatch(array $filters, array $data): bool;
}
