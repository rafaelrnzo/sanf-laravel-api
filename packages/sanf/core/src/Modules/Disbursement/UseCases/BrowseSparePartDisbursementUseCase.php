<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Constants\Pagination;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;
use Sanf\Core\Modules\Disbursement\Payloads\BrowseSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Integration\Entities\SanfCoreSparePartDisbursementEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class BrowseSparePartDisbursementUseCase implements ApplicationServiceInterface
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

    /**
     * @param BrowseSparePartDisbursementPayload $payload
     */
    public function execute($payload = null)
    {
        $payload->statusIds = [
            SparePartDisbursementStatusEnum::WAITING_VALIDATION,
            SparePartDisbursementStatusEnum::PAYMENT_COMPLETED,
            SparePartDisbursementStatusEnum::REJECTED,
            SparePartDisbursementStatusEnum::CANCELED,
        ];

        $data = $this->repository->list($payload);

        $total = $this->repository->listCount($payload);

        $page = 1;
        $perPage = Pagination::MAX_LIMIT;
        $responseCore = $this->sanfCoreApiClient->getSparePartDisbursementList($page, $perPage);

        $dataCore = collect($responseCore->data)->keyBy('batch_id');

        $data->transform(function (&$item) use ($dataCore) {
            /** 
             * @var SanfCoreSparePartDisbursementEntity $core
             * @var SparePartDisbursementModel $item
             */
            if ($core = $dataCore->get($item->batch_number)) {
                $item->status_id = $core->status_batch_id;
            }

            return $item;
        });

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $total,
                'count' => $data->count(),
                'skip' => (int) $payload->skip,
                'limit' => (int) $payload->limit,
                'sort_by' => $payload->sortBy,
            ],
        ];
    }
}