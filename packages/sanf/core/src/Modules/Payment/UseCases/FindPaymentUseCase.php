<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;

final class FindPaymentUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $xid, int $userAuthId, string $userProfileXid): ?PaymentModel
    {
        $data = $this->repository->find([
            'xid' => $xid,
            'user_auth_id' => $userAuthId,
            'user_profile_xid' => $userProfileXid,
        ]);

        optional($data)->load(['installments', 'activeMidtransTransaction']);

        return $data;
    }
}
