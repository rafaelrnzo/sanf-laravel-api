<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\SummaryBillContractDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SummaryBillContractService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param SummaryBillContractDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->internalApiClient->getContractDetail($dto->profile_xid, $dto->contract_no);
        $data = $response->data[$response->count - 1];
        $metadata = (object) [
            'total_amount' => $data->TOTAL_PEMBIAYAAN ?? 0,
            'total_penalty_amount' => $data->TOTAL_DENDA ?? 0,
            'total_paid_amount' => $data->TERBAYAR ?? 0,
            'total_outstanding_amount' => $data->TAGIHAN_SISA ?? 0,
            'total_invoice_amount' => ($data->INSTALL_AMT ?? 0) + ($data->TOTAL_DENDA ?? 0),
            'total_installment_amount' => $data->INSTALL_AMT ?? 0,
        ];

        try {
            $response = $this->internalApiClient->getFinancingUnitInvoice(
                $dto->profile_xid,
                $dto->contract_no,
                $dto->limit,
                $dto->skip,
                $dto->sort_by
            );
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

        $data = collect($response->data)->map(function ($item) {
            return (object) [
                'due_at' => $item->TGL_JATUHTEMPO ?? null,
                'bill_amount' => $item->TAGIHAN ?? 0,
                'penalty_amount' => $item->DENDA_PENALTY ?? 0,
                'currency_type' => $item->CURR_ID ?? null,
                'installment_index' => $item->ANG_KE ?? null,
            ];
        });

        return (object) [
            'data' => $data,
            'metadata' => $metadata,
            'paginate' => (object) [
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}
