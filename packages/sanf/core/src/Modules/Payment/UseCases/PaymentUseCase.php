<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;

final class PaymentUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function refreshLastStatusCheck(string $xid): bool
    {
        return $this->repository->updatePayment(
            ['xid' => $xid],
            ['last_checked_status_at' => Carbon::now()]
        );
    }

    public function findByXid(string $xid): ?PaymentModel
    {
        return $this->repository->find(['xid' => $xid]);
    }

    /**
     * @return Collection<PaymentModel>
     */
    public function getPendingExpiredList(): Collection
    {
        $statuses = [
            PaymentStatusEnum::PENDING,
            PaymentStatusEnum::EXPIRE_IN_PROGRESS,
        ];

        $filters = [
            ['expired_at', '<=', Carbon::now()],
        ];

        return $this->repository->getByStatuses($statuses, ['*'], $filters);
    }

    /**
     * @return Collection<PaymentModel>
     */
    public function getPendingSubmitInstallmentList(array $select = ['*']): Collection
    {
        $statuses = [
            PaymentStatusEnum::SUCCESS,
        ];

        $filters = [
            'core_installment_submitted' => false,
        ];

        return $this->repository->getByStatuses($statuses, $select, $filters);
    }
}
