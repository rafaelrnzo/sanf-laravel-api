<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Queries\BrowsePlafondDisbursementEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowsePlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private PlafondDisbursementRepositoryInterface $disbursementRepository;
    private SanfCoreApiClient $coreClient;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository,
        SanfCoreApiClient $coreClient
    ) {
        $this->disbursementRepository = $disbursementRepository;
        $this->coreClient = $coreClient;
    }

    /**
     * @param BrowsePlafondDisbursementRequestDto $dto
     */
    public function execute($dto = null)
    {
        /* @var BrowsePlafondDisbursementRequestDto $dto */

        $plafondDisbursementsCoreCollection = collect([]);
        try {
            $plafondDisbursementsCore = $this->coreClient->getPlafondDisbursement($dto->profileXid, null);
            $plafondDisbursementsCoreMap = array_map(function ($data) {
                return (object) [
                    'clientId' => $data['CUST_ID'],
                    'disbursementNo' => $data['DISBURSTMENT_NO'],
                    'plafondId' => $data['NO_PLAFOND'],
                    'bowheer' => $data['BOWHEER'],
                    'status' => $data['STATUS'],
                    'amount' => $data['AMOUNT'] ?? null,
                    'notes' => $data['NOTES'],
                ];
            }, $plafondDisbursementsCore['data']);

            $plafondDisbursementsCoreCollection = collect($plafondDisbursementsCoreMap);
        } catch (\Exception $exception) {
            report($exception);
            Log::warning('Core Exception');
        }

        $dto->limit = 1000;
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
        $plafondDisbursementsMap = array_map(function ($item) use ($plafondDisbursementsCoreCollection) {
            $plafondDisbursementsCore = $plafondDisbursementsCoreCollection->where('disbursementNo', '=', $item->disbursement_no)->first();

            $disbursement = $item;
            if ($plafondDisbursementsCore && in_array($plafondDisbursementsCore->status, PlafondDisbursementStatusEnum::APPROVE_CORE)) {
                $approveId = PlafondDisbursementStatusEnum::APPROVE;
                $approveStatus = (new PlafondDisbursementStatusEnum($approveId));
                $disbursement->status_id = $approveStatus->getValue();
                $disbursement->status = $approveStatus->getLabel();
            }

            return (object) $disbursement;
        }, $plafondDisbursements);

        return (object) [
            'data' => $plafondDisbursementsMap,
            'paginate' => (object) [
                'total' => (int) $plafondDisbursementsMap,
                'count' => count($plafondDisbursementsMap),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
