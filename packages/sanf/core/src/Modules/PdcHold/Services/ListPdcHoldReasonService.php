<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldReasonEloquentRepository;

/**
 * @since CR2025
 */
final class ListPdcHoldReasonService implements ApplicationServiceInterface
{
    protected PdcHoldReasonEloquentRepository $repository;

    public function __construct(PdcHoldReasonEloquentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $data = $this->repository->list($dto);

        return (object) [
            'data' => $data['lists'],
            'paginate' => (object) [
                'total' => (int) $data['total'],
                'count' => (int) $data['count'],
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => 'order',
            ],
        ];
    }
}
