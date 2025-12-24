<?php

namespace Sanf\Core\Modules\Payment\UseCases;

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

        return $paymentStatuses->map(fn ($item) => new PaymentStatusCountResponse($item->toArray()));
    }
}
