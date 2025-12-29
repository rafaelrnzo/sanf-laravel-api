<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\Payment\Responses\PaymentStatusCountResponse;

final class GetPaymentStatusCountUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(int $userAuthId, string $profileXid)
    {
        $filters = [
            'user_auth_id' => $userAuthId,
            'user_profile_xid' => $profileXid,
        ];

        $paymentStatuses = $this->repository->countByStatus($filters);

        $result = [];

        foreach (PaymentStatusEnum::values() as $status) {
            $paymentStatus = $paymentStatuses->firstWhere('status', $status->getValue());

            $result[] = new PaymentStatusCountResponse([
                'status' => $status->getValue(),
                'total' => optional($paymentStatus)->total ?? 0,
            ]);
        }

        return $result;
    }
}
