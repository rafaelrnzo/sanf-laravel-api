<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Queries\BrowsePlafondDisbursementEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;

final class BrowsePlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private PlafondDisbursementRepositoryInterface $disbursementRepository;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository
    ) {
        $this->disbursementRepository = $disbursementRepository;
    }

    /**
     * @param BrowsePlafondDisbursementRequestDto $dto
     */
    public function execute($dto = null)
    {
        /* @var BrowsePlafondDisbursementRequestDto $dto */

        $dto->limit = 10;
        $dto->skip = 0;
        $totalPlafondDisbursement = $this->disbursementRepository->count(new BrowsePlafondDisbursementEloquentBuilder($dto));
        if ($totalPlafondDisbursement === 0) {
            return (object) [
                'data' => [],
                'paginate' => (object) [
                    'total' => (int) $totalPlafondDisbursement,
                    'count' => (int) $totalPlafondDisbursement,
                    'skip' => (int) $dto->skip,
                    'limit' => (int) $dto->limit,
                    'sort_by' => $dto->sortBy,
                ],
            ];
        }

        $dto->limit = 10;
        $dto->skip = 0;
        $plafondDisbursements = $this->disbursementRepository->query(new BrowsePlafondDisbursementEloquentBuilder($dto));

        return (object) [
            'data' => $plafondDisbursements,
            'paginate' => (object) [
                'total' => (int) $totalPlafondDisbursement,
                'count' => count($plafondDisbursements),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
