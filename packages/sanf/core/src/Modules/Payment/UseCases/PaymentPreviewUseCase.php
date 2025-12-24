<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Sanf\Core\Modules\Payment\Enums\PaymentPreviewPlatformEnum;
use Sanf\Core\Modules\Payment\Models\PaymentPreviewModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentPreviewRepositoryInterface;

final class PaymentPreviewUseCase
{
    protected PaymentPreviewRepositoryInterface $repository;

    public function __construct(
        PaymentPreviewRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function find(string $profileXid): ?PaymentPreviewModel
    {
        $filters = [
            'user_profile_xid' => $profileXid,
            'platform' => PaymentPreviewPlatformEnum::MOBILE,
        ];

        return $this->repository->find($filters);

    }

    public function clear(string $profileXid): bool
    {
        $filters = [
            'user_profile_xid' => $profileXid,
            'platform' => PaymentPreviewPlatformEnum::MOBILE,
        ];

        return $this->repository->delete($filters);
    }
}
