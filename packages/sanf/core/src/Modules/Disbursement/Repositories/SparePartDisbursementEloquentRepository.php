<?php

namespace Sanf\Core\Modules\Disbursement\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementBatchModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementDocumentModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;

class SparePartDisbursementEloquentRepository extends AbstractEloquentRepository implements SparePartDisbursementRepositoryInterface
{
    protected SparePartDisbursementModel $disbursementModel;
    protected SparePartDisbursementBatchModel $disbursementBatchModel;
    protected SparePartDisbursementInvoiceModel $invoiceModel;
    protected SparePartDisbursementDocumentModel $documentModel;

    public function __construct(
        SparePartDisbursementModel $disbursementModel,
        SparePartDisbursementInvoiceModel $invoiceModel,
        SparePartDisbursementBatchModel $disbursementBatchModel,
        SparePartDisbursementDocumentModel $documentModel
    ) {
        $this->disbursementModel = $disbursementModel;
        $this->invoiceModel = $invoiceModel;
        $this->disbursementBatchModel = $disbursementBatchModel;
        $this->documentModel = $documentModel;
    }

    public function listQuery(object $params)
    {
        $plafondXid = $params->plafondXid;
        $statusIds = $params->statusIds ?? [];
        $statusId = $params->statusId;
        $keyword = $params->keyword;

        return $this->disbursementModel->newQuery()
            ->where('customer_id_sanfind', '=', $params->profileXid)
            ->when($plafondXid, function ($query, $plafondXid) {
                $query->where('plafond_no', $plafondXid);
            })
            ->when(!empty($statusIds), function ($query) use ($statusIds) {
                return $query->whereIn('status_id', $statusIds);
            })
            ->when($statusId, function ($query, $statusId) {
                return $query->where('status_id', $statusId);
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('disbursement_no', 'ilike', "%{$keyword}%");
            });
    }

    /**
     * @param object $params
     * @return Collection<SparePartDisbursementModel>
     */
    public function list(object $params): Collection
    {
        $sortBy = $params->sortBy;
        $skip = $params->skip;
        $limit = $params->limit;

        switch ($sortBy) {
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

        return $this->listQuery($params)
            ->with(['supplier'])
            ->when($skip, function ($query, $skip) {
                return $query->skip($skip);
            })
            ->when($limit, function ($query, $limit) {
                return $query->limit($limit);
            })
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function listCount(object $params): int
    {
        return $this->listQuery($params)->count();
    }

    public function find(array $filters): ?SparePartDisbursementModel
    {
        return $this->disbursementModel->newQuery()->where($filters)->first();
    }

    public function update(array $filters, array $data): bool
    {
        $model = $this->disbursementModel->newQuery()->where($filters)->first();

        return $model->update($data);
    }

    /**
     * @param array $select
     * @param array $filters
     * @return Collection<SparePartDisbursementModel>
     */
    public function get(array $select = ['*'], array $filters = [], array $relations = []): Collection
    {
        return $this->disbursementModel->newQuery()
            ->with($relations)
            ->select($select)
            ->where($filters)
            ->get();
    }

    public function findInvoice(array $filters): ?SparePartDisbursementInvoiceModel
    {
        return $this->invoiceModel->newQuery()->where($filters)->first();
    }

    public function listInvoice(array $filters): Collection
    {
        return $this->invoiceModel->newQuery()->where($filters)->get();
    }

    public function listInvoiceReadySubmit(array $filters): Collection
    {
        return $this->invoiceModel->newQuery()
            ->where($filters)
            ->whereIn('status_id', [
                SparePartDisbursementStatusEnum::APPROVED,
                SparePartDisbursementStatusEnum::CUSTOMER_REJECTED,
            ])
            ->get();
    }

    public function rejectInvoices(array $filters, array $invoiceXids): int
    {
        return $this->invoiceModel->newQuery()
            ->where($filters)
            ->whereIn('xid', $invoiceXids)
            ->update([
                'status_id' => SparePartDisbursementStatusEnum::CUSTOMER_REJECTED,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function approveInvoicesWithExclusion(array $filters, array $excludeInvoiceXids): int
    {
        return $this->invoiceModel->newQuery()
            ->where($filters)
            ->whereNotIn('xid', $excludeInvoiceXids)
            ->where('status_id', '!=', SparePartDisbursementStatusEnum::REJECTED) // exclude rejected by core
            ->update([
                'status_id' => SparePartDisbursementStatusEnum::APPROVED,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function updateInvoice(array $filters, array $data): bool
    {
        $model = $this->invoiceModel->newQuery()->where($filters)->first();

        return $model->update($data);
    }

    public function findBatch(array $filters): ?SparePartDisbursementBatchModel
    {
        return $this->disbursementBatchModel->newQuery()->where($filters)->first();
    }

    public function listUploadedDocument(array $filters): Collection
    {
        return $this->documentModel->newQuery()
            ->where($filters)
            ->whereNotNull('doc_file')
            ->get();
    }

    public function updateBatch(array $filters, array $data): bool
    {
        $model = $this->disbursementBatchModel->newQuery()->where($filters)->first();

        return $model->update($data);
    }
}
