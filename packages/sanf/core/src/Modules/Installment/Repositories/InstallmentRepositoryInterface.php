<?php

namespace Sanf\Core\Modules\Installment\Repositories;

use Illuminate\Support\Collection;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;

interface InstallmentRepositoryInterface
{
    public function create(array $data): InstallmentModel;

    public function listByContracts(array $contractNums, array $dueDates): Collection;

    public function existsByContractsStatus(array $contractNums, array $dueDates, string $status): bool;

    public function update(array $filters, array $data): bool;
}
