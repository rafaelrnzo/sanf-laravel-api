<?php

namespace Sanf\Core\Modules\StandbyFinancing\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\StandbyFinancing\Enums\StandbyFinancingStateEnum;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingBankAccountModel;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingDocumentModel;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingInvoiceModel;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingPlafondLockModel;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingStateHistoryModel;

class EloquentStandbyFinancingRepository implements StandbyFinancingRepositoryInterface
{
    public function countApplicationsInMonth(string $yearMonth): int
    {
        return StandbyFinancingApplicationModel::query()
            ->where('recap_id_b2b', 'like', "SF{$yearMonth}%")
            ->count();
    }

    public function invoiceExistsInActiveRequest(string $invoiceNumber, array $activeStatuses): bool
    {
        return StandbyFinancingInvoiceModel::query()
            ->where('invoice_number', $invoiceNumber)
            ->whereIn('invoice_status', $activeStatuses)
            ->exists();
    }

    public function sumLockedAmount(string $noPlafond): float
    {
        return (float) StandbyFinancingPlafondLockModel::query()
            ->where('no_plafond', $noPlafond)
            ->where('lock_status', StandbyFinancingStateEnum::LOCK_STATUS_LOCKED)
            ->sum('locked_amount');
    }

    public function createSubmission(array $application, array $invoices, array $bankAccount, array $documents, array $actor): StandbyFinancingApplicationModel
    {
        return DB::transaction(function () use ($application, $invoices, $bankAccount, $documents, $actor) {
            $model = StandbyFinancingApplicationModel::query()->create($application);

            foreach ($invoices as $invoice) {
                StandbyFinancingInvoiceModel::query()->create($invoice + [
                    'application_id' => $model->id,
                ]);
            }

            StandbyFinancingBankAccountModel::query()->create($bankAccount + [
                'application_id' => $model->id,
            ]);

            foreach ($documents as $document) {
                StandbyFinancingDocumentModel::query()->create($document + [
                    'application_id' => $model->id,
                ]);
            }

            StandbyFinancingStateHistoryModel::query()->create([
                'application_id' => $model->id,
                'from_state' => null,
                'to_state' => StandbyFinancingStateEnum::SUBMITTED,
                'action' => 'SUBMIT',
                'notes' => null,
                'actor_type' => 'CUSTOMER',
                'actor_id' => $actor['id'] ?? null,
                'actor_name' => $actor['name'] ?? null,
                'created_at' => CarbonImmutable::now(),
            ]);

            StandbyFinancingPlafondLockModel::query()->create([
                'application_id' => $model->id,
                'no_plafond' => $model->no_plafond,
                'locked_amount' => $model->total_amount,
                'lock_status' => StandbyFinancingStateEnum::LOCK_STATUS_LOCKED,
                'locked_at' => CarbonImmutable::now(),
                'reason' => 'standby_financing_submission',
            ]);

            return $model->fresh(['invoices', 'bankAccount', 'documents', 'plafondLock']);
        });
    }

    public function browseByCustomer(string $customerId, int $page, int $perPage): array
    {
        return StandbyFinancingApplicationModel::query()
            ->with(['invoices'])
            ->where('cust_id', $customerId)
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->all();
    }

    public function countByCustomer(string $customerId): int
    {
        return StandbyFinancingApplicationModel::query()
            ->where('cust_id', $customerId)
            ->count();
    }

    public function findByRecapId(string $recapId, ?string $customerId = null): ?StandbyFinancingApplicationModel
    {
        $query = StandbyFinancingApplicationModel::query()
            ->with(['invoices', 'bankAccount', 'documents'])
            ->where('recap_id_b2b', $recapId);

        if (!is_null($customerId)) {
            $query->where('cust_id', $customerId);
        }

        return $query->first();
    }
}
