<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Core\Modules\Disbursement\Supports\SparePartDisbursementStatusResolver;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class FindSparePartDisbursementUseCase
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

    public function execute(string $profileXid, string $disbursementXid): ?SparePartDisbursementModel
    {
        $data = $this->repository->find([
            'xid' => $disbursementXid,
            'customer_id_sanfind' => $profileXid,
        ]);

        if ($data === null) {
            return null;
        }

        $data->load('validInvoices');

        $dataCore = $this->sanfCoreApiClient->getSparePartDisbursementDetail($data->batch_number, $data->customer_id);

        if ($dataCore) {
            $data->status_id = SparePartDisbursementStatusResolver::mapFromCore($dataCore->status_batch_id);
        }

        return $data;
    }
}
