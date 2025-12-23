<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;

final class FindSparePartDisbursementInvoiceUseCase
{
    protected SparePartDisbursementRepositoryInterface $repository;

    public function __construct(
        SparePartDisbursementRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $profileXid, string $disbursementXid, string $invoiceXid): ?SparePartDisbursementInvoiceModel
    {
        $data = $this->repository->findInvoice([
            'xid' => $invoiceXid,
            'disbursement_xid' => $disbursementXid,
            'customer_id_sanfind' => $profileXid,
        ]);

        return $data;
    }
}
