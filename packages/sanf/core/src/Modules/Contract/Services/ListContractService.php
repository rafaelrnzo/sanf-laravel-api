<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ListContractDto;
use Sanf\Core\Modules\Contract\Enums\ContractTypeEnum;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\SanfInternalApiException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class ListContractService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param ListContractDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $this->getUser($dto);

        try {
            $response = $this->internalApiClient->getContractList(
                $dto->profile_xid,
                $dto->contract_type,
                $dto->limit,
                $dto->skip,
                $dto->sort_by,
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

        $timezoneOffset = $dto->tz_offset;
        $todayTimestampWithTz = CarbonImmutable::now()->timestamp + $timezoneOffset;
        $todayWithTz = CarbonImmutable::parse($todayTimestampWithTz);
        $data = $this->mappingResponse($response, $todayWithTz, $timezoneOffset);
        $filterData = $this->filterDataByContractStatus($dto, $data);

        return (object) [
            'data' => $filterData,
            'paginate' => (object) [
                'total' => $response->total ?? $response->count,
                'count' => count($filterData) ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }

    private function getUser(?ListContractDto $dto): void
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }
    }

    private function countDiffDays(string $overdueDate, CarbonImmutable $todayWithTz, ?int $timezoneOffset = 0): int
    {
        $overdueTimestampWithTz = CarbonImmutable::make($overdueDate)->timestamp + $timezoneOffset;
        $overdueWithTz = CarbonImmutable::parse($overdueTimestampWithTz);
        $diffTime = $todayWithTz->startOfDay()->diff($overdueWithTz->startOfDay());
        $diffDays = (int) -"{$diffTime->days}";
        if ($diffTime->invert) {
            $diffDays = (int) "{$diffTime->days}";
        }

        return $diffDays;
    }

    private function mappingResponse(
        $response,
        CarbonImmutable $todayWithTz,
        ?int $timezoneOffset
    ): array {
        return array_map(function ($item) use ($todayWithTz, $timezoneOffset) {
            if (is_null($item->TGL_TENGGAT_PEMBAYARAN)) {
                throw new SanfInternalApiException('TGL TENGGAT PEMBAYARAN got null value');
            }

            $totalDiffDays = $this->countDiffDays($item->TGL_TENGGAT_PEMBAYARAN, $todayWithTz, $timezoneOffset);

            return (object) [
                'contract_at' => $item->TGL_KONTRAK ?? null,
                'contract_no' => $item->NO_KONTRAK ?? null,
                'financing_type' => (object) [
                    'id' => null,
                    'name' => $item->JENIS_PEMBIAYAAN ?? null,
                ],
                'total_amount' => $item->TOTAL_PEMBIAYAAN ?? 0,
                'currency_type' => $item->CURR_ID ?? null,
                'payment_due_at' => $item->TGL_TENGGAT_PEMBAYARAN ?? null,
                'days' => $totalDiffDays,
            ];
        }, $response->data);
    }

    private function filterDataByContractStatus(ListContractDto $dto, array $data): array
    {
        if ($dto->contract_status === ContractTypeEnum::SETTLED) {
            return $data;
        }

        return array_filter($data, function ($response) use ($dto) {
            if ($dto->contract_status === ContractTypeEnum::OVERDUE) {
                return $response->days > 0;
            }

            return $response->days <= 0;
        });
    }
}
