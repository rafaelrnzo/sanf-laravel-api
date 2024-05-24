<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Queries\ReadPlafondDisbursementEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;

final class ReadPlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private PlafondDisbursementRepositoryInterface $disbursementRepository;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository
    ) {
        $this->disbursementRepository = $disbursementRepository;
    }

    public function execute($dto = null)
    {
        $plafondDisbursements = $this->disbursementRepository->query(new ReadPlafondDisbursementEloquentBuilder($dto));
        if (count($plafondDisbursements) === 0) {
            throw new PlafondDisbursementNotFoundException();
        }

        return $plafondDisbursements[0];
    }
}
