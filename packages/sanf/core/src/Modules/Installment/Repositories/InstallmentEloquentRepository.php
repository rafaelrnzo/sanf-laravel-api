<?php

namespace Sanf\Core\Modules\Installment\Repositories;

use Illuminate\Support\Collection;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;

class InstallmentEloquentRepository implements InstallmentRepositoryInterface
{
    protected InstallmentModel $installmentModel;

    public function __construct(InstallmentModel $installmentModel)
    {
        $this->installmentModel = $installmentModel;
    }

    public function create(array $data): InstallmentModel
    {
        /**
         * @var InstallmentModel
         */
        $model = $this->installmentModel->newQuery()->create($data);

        return $model;
    }

    public function listByContracts(array $contractNums, array $dueDates, string $userProfileXid): Collection
    {
        return $this->installmentModel->newQuery()
            ->whereIn('contract_no', $contractNums)
            ->whereIn('due_date', $dueDates)
            ->where('user_profile_xid', $userProfileXid)
            ->get();
    }

    public function existsByContractsStatus(array $contractNums, array $dueDates, string $status, string $userProfileXid): bool
    {
        return $this->installmentModel->newQuery()
            ->whereIn('contract_no', $contractNums)
            ->whereIn('due_date', $dueDates)
            ->where('status', $status)
            ->where('user_profile_xid', $userProfileXid)
            ->exists();
    }

    public function update(array $filters, array $data): bool
    {
        $model = $this->installmentModel->newQuery()->where($filters)->first();

        if ($model === null) {
            return false;
        }

        return (bool) $model->update($data);
    }

    public function findByContractsAndDueDates(array $contractDueDates, string $userProfileXid): Collection
    {
        if (empty($contractDueDates)) {
            return Collection::make();
        }

        $uniquePairs = $this->deduplicatePairs($contractDueDates);

        return $this->installmentModel->newQuery()
            ->select(['id', 'contract_no', 'due_date', 'status'])
            ->with(['payments' => fn ($q) => $q->orderByDesc('created_at')])
            ->where('user_profile_xid', $userProfileXid)
            ->where(function ($query) use ($uniquePairs) {
                foreach ($uniquePairs as $index => $pair) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $query->{$method}(function ($subQuery) use ($pair) {
                        $subQuery->where('contract_no', $pair['contract_no'])
                            ->whereDate('due_date', $pair['due_date']);
                    });
                }
            })
            ->get();
    }

    private function deduplicatePairs(array $pairs): array
    {
        $map = [];
        foreach ($pairs as $pair) {
            if (empty($pair['contract_no']) || empty($pair['due_date'])) {
                continue;
            }

            $key = sprintf('%s|%s', $pair['contract_no'], $pair['due_date']);
            $map[$key] = $pair;
        }

        return array_values($map);
    }
}
