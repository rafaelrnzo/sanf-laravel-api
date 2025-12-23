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

    public function listByContracts(array $contractNums, array $dueDates): Collection
    {
        return $this->installmentModel->newQuery()
            ->whereIn('contract_no', $contractNums)
            ->whereIn('due_date', $dueDates)
            ->get();
    }

    public function existsByContractsStatus(array $contractNums, array $dueDates, string $status): bool
    {
        return $this->installmentModel->newQuery()
            ->whereIn('contract_no', $contractNums)
            ->whereIn('due_date', $dueDates)
            ->where('status', $status)
            ->exists();
    }

    public function update(array $filters, array $data): bool
    {
        $model = $this->installmentModel->newQuery()->where($filters)->first();

        return $model->update($data);
    }
}
