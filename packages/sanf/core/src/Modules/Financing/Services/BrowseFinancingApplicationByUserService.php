<?php

namespace Sanf\Core\Modules\Financing\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\BrowseFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Enums\FinancingStatusEnum;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class BrowseFinancingApplicationByUserService implements ApplicationServiceInterface
{
    protected FinancingApplicationRepositoryInterface $financingApplicationRepository;
    protected FinancingApplicationSpecificationFactoryInterface $financingSpecificationFactory;
    protected AuthModel $userRepository;
    protected SanfCoreApiClient $client;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingApplicationSpecificationFactoryInterface $financingSpecificationFactory,
        AuthModel $userRepository,
        SanfCoreApiClient $client
    ) {
        $this->financingSpecificationFactory = $financingSpecificationFactory;
        $this->userRepository = $userRepository;
        $this->client = $client;
        $this->financingApplicationRepository = $financingApplicationRepository;
    }

    /**
     * @param BrowseFinancingApplicationDto $dto
     * @return object
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $coreData = collect([]);
        try {
            $result = $this->client->browseFinancingApplication($user->username, $dto->xid);
            $resultMapping = array_map(function ($data) {
                return (object) [
                    'xid' => $data['XID'],
                    'application_code' => $data['APPLICATION_CODE'],
                    'status_id' => (new FinancingStatusEnum($data['STATUS_NAME']))->getStatusId(),
                    'status_name' => $data['STATUS_NAME'],
                    'financing_object_count' => $data['COUNT'],
                    'financing_facility_name' => $data['FACILITY_NAME'] ?? null,
                    'financing_method_name' => $data['METHOD_NAME'] ?? null,
                    'created_at' => Carbon::parse($data['CREATED_AT']),
                ];
            }, $result['data']);

            $coreData = collect($resultMapping);
        } catch (\Exception $exception) {
            report($exception);
            Log::warning('Core Exception');
        }

        $total = $this->financingApplicationRepository->size(
            $this->financingSpecificationFactory->paginateByUser(
                $dto->userId,
                $dto->xid,
                null,
                null,
                null,
                $dto->keyword
            )
        );

        $result = $this->financingApplicationRepository->query(
            $this->financingSpecificationFactory->paginateByUser(
                $dto->userId,
                $dto->xid,
                $dto->skip,
                $dto->limit,
                $dto->sortBy,
                $dto->keyword
            )
        );

        $mappingData = array_map(function ($data) use ($coreData) {
            $core = $coreData->where('application_code', '=', $data->application_code)->first();

            return (object) [
                'xid' => $data->xid,
                'application_code' => $data->application_code,
                'status_id' => ($core) ? $core->status_id : $data->status->id,
                'status_name' => ($core) ? $core->status_name : $data->status->name,
                'financing_object_count' => $data->total_object ?? count($data->objects),
                'financing_facility_name' => optional($data->facility)->name,
                'financing_method_name' => optional($data->method)->name,
                'created_at' => $data->created_at,
            ];
        }, $result);

        return (object) [
            'data' => collect($mappingData)->sortByDesc('created_at'),
            'paginate' => (object) [
                'total' => (int) $total,
                'count' => count($mappingData),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
