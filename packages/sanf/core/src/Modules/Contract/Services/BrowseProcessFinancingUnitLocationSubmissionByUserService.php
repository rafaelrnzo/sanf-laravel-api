<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;

final class BrowseProcessFinancingUnitLocationSubmissionByUserService extends
    FinancingUnitLocationSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $submissions = $this->repository->query(
            $this->specificationFactory->getWhereContractNumberAndIsProcess($dto->userId, $dto->xid)
        );

        $response = $this->internalApiClient->getFinancingUnitSubmissionItem(
            $user->personal_xid,
            $dto->xid,
            $dto->limit,
            $dto->skip,
            $dto->sortBy,
        );

        $collection = collect($submissions);
        $data = collect($response->data)->map(function ($item) use ($collection) {
            $submission = $collection->where('serial_no', $item->SERIAL_NO)->first();
            $metadata = json_decode($submission->submitted_location_metadata ?? "");
            return (object)[
                'serial_no' => $item->SERIAL_NO ?: null,
                'brand_type_model' => $item->BTM ?: null,
                'provider_name' => null,
                'year' => $item->YEAR,
                'location_metadata' => (object)[
                    'city_id' => $item->CITY_ID,
                    'city_name' => $item->CITY
                ],
                'status' => ($submission) ? (object)[
                    'id' => $submission->status->id,
                    'name' => $submission->status->name
                ] : null,
                'submitted_location_metadata' => ($submission) ? (object)[
                    'city_id' => $metadata->city_id,
                    'city_name' => $metadata->city_name,
                ] : null,
            ];
        });

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
