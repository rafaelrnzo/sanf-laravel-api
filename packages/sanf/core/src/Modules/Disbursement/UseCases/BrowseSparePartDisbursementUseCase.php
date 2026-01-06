<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Sanf\Core\Constants\Pagination;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;
use Sanf\Core\Modules\Disbursement\Payloads\BrowseSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Core\Modules\Disbursement\Supports\SparePartDisbursementStatusResolver;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class BrowseSparePartDisbursementUseCase
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

    public function execute(BrowseSparePartDisbursementPayload $payload)
    {
        if ($payload->listType == 'HISTORICAL') {
            $payload->statusIds = [
                SparePartDisbursementStatusEnum::WAITING_VALIDATION,
                SparePartDisbursementStatusEnum::PAYMENT_COMPLETED,
                SparePartDisbursementStatusEnum::REJECTED,
                SparePartDisbursementStatusEnum::CANCELED,
                SparePartDisbursementStatusEnum::NEED_REVIEW,
            ];
        } elseif ($payload->listType == 'NEED_APPROVAL') {
            $payload->statusIds = [
                SparePartDisbursementStatusEnum::WAITING_CUSTOMER,
            ];
        }

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
                $item->status_id = SparePartDisbursementStatusResolver::mapFromCore($core->status_batch_id);
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
