<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Illuminate\Support\Collection;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

interface PaymentRepositoryInterface
{
    public function list(object $params): Collection;

    public function listCount(object $params): int;

    public function find(array $filters): ?PaymentModel;

    public function findLatestInsallmentPayment(int $installmentId, array $filters = []): ?PaymentModel;

    public function findActivePendingPaymentByContract(
        int $userAuthId,
        string $userProfileXid,
        string $contractNo
    ): ?PaymentModel;

    public function findActivePendingPaymentByContractAndDueDate(
        int $userAuthId,
        string $userProfileXid,
        string $contractNo,
        string $dueDate
    ): ?PaymentModel;

    public function findByInstallmentDetail(string $contractNo, string $dueDate, array $filters = []): ?PaymentModel;

    public function findByMidtransOrder(string $midtransOrderId, array $filters = []): ?PaymentModel;

    public function findByMidtransTransaction(string $midtransTransactionId, array $filters = []): ?PaymentModel;

    public function create(array $data): PaymentModel;

    public function countByStatus(array $filters): Collection;

    public function createInstallments(array $filters, array $installments): void;

    public function createMidtransTransaction(array $data): MidtransTransactionModel;

    public function updateMidtransTransaction(array $filters, array $data): bool;

    public function deleteMidtransTransaction(array $filters): bool;

    public function findMidtransTransaction(array $filters): ?MidtransTransactionModel;

    public function updatePayment(array $filters, array $data): bool;

    /**
     * @return Collection<PaymentModel>
     */
    public function getByStatuses(array $statuses, array $select = ['*'], array $filters = []): Collection;
}
