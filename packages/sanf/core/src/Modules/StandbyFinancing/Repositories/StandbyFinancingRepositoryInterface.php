<?php

namespace Sanf\Core\Modules\StandbyFinancing\Repositories;

use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;

interface StandbyFinancingRepositoryInterface
{
    public function countApplicationsInMonth(string $yearMonth): int;

    public function invoiceExistsInActiveRequest(string $invoiceNumber, array $activeStatuses): bool;

    public function sumLockedAmount(string $noPlafond): float;

    public function createSubmission(array $application, array $invoices, array $bankAccount, array $documents, array $actor): StandbyFinancingApplicationModel;

    public function browseByCustomer(string $customerId, int $page, int $perPage): array;

    public function countByCustomer(string $customerId): int;

    public function findByRecapId(string $recapId, ?string $customerId = null): ?StandbyFinancingApplicationModel;
}
