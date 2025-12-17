<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class FindSparePartDisbursementInvoiceUseCase
{
    protected SparePartDisbursementRepositoryInterface $repository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        SparePartDisbursementRepositoryInterface $repository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->repository = $repository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(string $profileXid, string $disbursementXid, string $invoiceXid): ?SparePartDisbursementInvoiceModel
    {
        $data = $this->repository->findInvoice([
            'xid' => $invoiceXid,
            'disbursement_xid' => $disbursementXid,
            'customer_id' => $profileXid,
        ]);

        return $data;
    }
}
