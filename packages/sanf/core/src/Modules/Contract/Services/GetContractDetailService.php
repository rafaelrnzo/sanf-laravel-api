<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetContractDetailService extends UserService implements ApplicationServiceInterface
{

    protected SanfCoreApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param $dto
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

        return (object)[
            'contract_at' => $data->TGL_KONTRAK ?? null,
            'contract_no' => $data->NO_KONTRAK ?? null,
            'currency_type' => $data->CURR_ID ?? null,
            'status' => (object)[
                'id' => $data->STATUS_ID ?? null,
                'name' => $data->STATUS ?? null,
            ],
            'total_amount' => $data->TOTAL_PEMBIAYAAN ?? 0,
            'total_invoice_amount' => ($data->INSTALL_AMT ?? 0) + ($data->TOTAL_DENDA ?? 0),
            'total_installment_amount' => $data->INSTALL_AMT ?? 0,
            'total_installment' => $data->TENOR ?? 0,
            'total_penalty_amount' => $data->TOTAL_DENDA ?? 0,
            'total_paid_amount' => $data->TERBAYAR ?? 0,
            'total_outstanding_amount' => $data->TAGIHAN_SISA ?? 0,
            'due_at' => $data->DT_DUE ?? null,
            'installment_count' => $data->ANGSURAN_KE ?? 0,
            'financing' => (object)[
                'due_at' => $data->DT_DUE ?? null,
                'finished_at' => $data->TGL_SELESAI ?? null,
                'interest_percentage' => $data->RATE_EFF ?? 0,
                'facility' => (object)[
                    'id' => $data->ID_JENIS_PEMBIAYAAN ?? null,
                    'name' => $data->JENIS_PEMBIAYAAN ?? null,
                ],
                'method' => (object)[
                    'id' => $data->ID_CARA_PEMBIAYAAN ?? null,
                    'name' => $data->CARA_PEMBIAYAAN ?? null,
                ],
                'total_tenor' => $data->TENOR ?? 0
            ],
            'total_financing_unit' => $data->TOT_UNIT ?? 0,
        ];
    }
}
