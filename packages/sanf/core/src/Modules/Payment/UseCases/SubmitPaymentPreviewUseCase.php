<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Sanf\Core\Modules\Payment\Entities\PaymentPreviewInstallmentEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentPreviewPlatformEnum;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\Repositories\PaymentPreviewRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class SubmitPaymentPreviewUseCase
{
    protected PaymentPreviewRepositoryInterface $repository;

    public function __construct(PaymentPreviewRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $profileXid
     * @param PaymentInstallmentPayload[] $installments
     */
    public function execute(string $profileXid, array $installments)
    {
        $attributes = [
            'user_profile_xid' => $profileXid,
            'platform' => PaymentPreviewPlatformEnum::MOBILE,
        ];

        $data = ['installments' => $this->mapInstallments($installments)];

        return $this->repository->updateOrCreate($attributes, $data);
    }

    private function mapInstallments(array $installments)
    {
        return array_map(fn (PaymentInstallmentPayload $item) => new PaymentPreviewInstallmentEntity([
            'contract_no' => $item->contract_no,
            'due_date' => Carbon::createFromTimestamp($item->due_date, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->toIso8601String(),
        ]), $installments);
    }
}
