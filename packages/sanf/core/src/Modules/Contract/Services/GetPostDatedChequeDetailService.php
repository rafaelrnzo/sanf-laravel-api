<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetPostDatedChequeDetailService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;
    protected PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SanfCoreApiClient $internalApiClient,
        PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository
    ) {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
        $this->pdcHoldGiroRepository = $pdcHoldGiroRepository;
    }

    /**
     * @param PostDatedChequeDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        try {
            $response = $this->internalApiClient->getPdcGiroByContractV2(
                $dto->profile_xid,
                [$dto->contract_no],
                optional($dto->date_start)->format('Y-m-d'),
                optional($dto->date_end)->format('Y-m-d'),
                $dto->status_id,
                $dto->limit,
                $dto->skip,
                $dto->sort_by
            );

            $isForPdcHold = isset($dto->date_start, $dto->date_end); // Not neat, but mobile use same endpoint

            if ($isForPdcHold) {
                $alreadySubmitted = $this->pdcHoldGiroRepository->getSubmittedWithSubmissions([
                    'id',
                    'pdc_hold_id',
                    'pdc_resume_id',
                    'customer_id',
                    'contract_no',
                    'pdc_no',
                    'pdc_type',
                    'created_at',
                ], [
                    ['customer_id', '=', $dto->profile_xid],
                ]);
            }

            $data = [];
            $filterCount = 0;

            foreach ($response->data as $item) {
                if (isset($item->PDC_NO)) {
                    if (
                        $isForPdcHold
                        && isset($alreadySubmitted)
                        && isset($item->AGREE_NO, $item->PDC_TYPE)
                        && $submitted = $alreadySubmitted
                        ->where('pdc_no', $item->PDC_NO)
                        ->where('contract_no', $item->AGREE_NO)
                        ->where('pdc_type', $item->PDC_TYPE)
                        ->sortByDesc('created_at')
                        ->first()
                    ) {
                        if (!isset($submitted->pdc_resume) || $submitted->pdc_resume->status_id !== PdcHoldStatusEnum::ACCEPTED) {
                            // Is submitted, skip this
                            continue;
                        }
                    }

                    // workaround filter giro no after change to V2
                    if (
                        !empty($dto->keyword)
                        && $dto->keyword !== ''
                        && stripos($item->PDC_NO, $dto->keyword) === false
                    ) {
                        continue;
                    }

                    $filterCount++;
                }

                $data[] = (object) [
                    'pdc_no' => $item->PDC_NO ?? null,
                    'amount' => $item->PDC_AMT ?? 0,
                    'currency_type' => $item->CURR_ID ?? null,
                    'submitted_date' => $item->PDC_DUE_DT ?? null,
                    'pdc_type' => $item->PDC_TYPE ?? null,
                    'status' => (object) [
                        'id' => $item->STATUS_ID ?? null,
                        'name' => $item->STATUS ?? null,
                    ],
                ];
            }
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object) [
                'data' => [],
                'paginate' => (object) [
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int) $dto->skip,
                    'limit' => (int) $dto->limit,
                    'sortBy' => $dto->sort_by,
                ],
            ];
        }

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $response->total ?? $response->count,
                'count' => $filterCount ?: $response->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}
