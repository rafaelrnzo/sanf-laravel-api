<?php

namespace Sanf\Core\Modules\Prepayment\Services;


use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSimulationByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSimulationByUserResponseDto;
use Sanf\Core\Modules\Prepayment\Exceptions\PrepaymentSimulationNotFoundException;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class AddPrepaymentSimulationByUserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $apiClient;

    /**
     * BrowseContractByUserService constructor.
     * @param SanfCoreApiClient $apiClient
     */
    public function __construct(SanfCoreApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param AddPrepaymentSimulationByUserRequestDto $dto
     * @return AddPrepaymentSimulationByUserResponseDto
     */
    public function execute($dto = null)
    {
        try {
            $result = $this->apiClient->getPrepaymentDetail($dto->contractNo, $dto->prepaymentDate);
            $prepayment = $result->data;
            $items = array_map(function ($item) {
                return (object)[
                    'description' => $item->DESCRIPTION,
                    'amount' => $item->JUMLAH,
                ];
            }, $prepayment->ITEM);
            $data = (object)[
                'contractNo' => $prepayment->NO_KONTRAK,
                'totalPrepayment' => $prepayment->TOTAL_PAYMENT,
                'prepaymentDate' => CarbonImmutable::createFromFormat('dmY', $prepayment->TGL_PREPAY),
                'currencyType' => $prepayment->CURR_ID,
                'items' => $items,
            ];
        } catch (SanfInternalApiDataNotFoundException $exception) {
            throw new PrepaymentSimulationNotFoundException();
        }
        return new AddPrepaymentSimulationByUserResponseDto([
            'contractNo' => $data->contractNo,
            'prepaymentDate' => $data->prepaymentDate,
            'totalPrepayment' => $data->totalPrepayment,
            'currencyType' => $data->currencyType,
            'items' => $data->items,
        ]);
    }
}
