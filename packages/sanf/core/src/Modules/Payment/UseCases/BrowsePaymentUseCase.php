<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Sanf\Core\Modules\Payment\Payloads\BrowsePaymentPayload;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;

final class BrowsePaymentUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(BrowsePaymentPayload $payload)
    {
        $data = $this->repository->list($payload);

        $total = $this->repository->listCount($payload);

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
