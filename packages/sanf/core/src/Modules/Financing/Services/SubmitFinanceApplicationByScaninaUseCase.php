<?php

namespace Sanf\Core\Modules\Financing\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Enums\FinancingApplicationTypeEnum;
use Sanf\Core\Modules\Financing\Enums\FinancingStatusEnum;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationLimitExceedException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class SubmitFinanceApplicationByScaninaUseCase implements ApplicationServiceInterface
{
    private $financingApplicationRepository;
    private $financingSpecificationFactory;
    private $coreClient;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingApplicationSpecificationFactoryInterface $financingSpecificationFactory,
        SanfCoreApiClient $coreClient
    ) {
        $this->financingApplicationRepository = $financingApplicationRepository;
        $this->financingSpecificationFactory = $financingSpecificationFactory;
        $this->coreClient = $coreClient;
    }

    public function execute($dto = null)
    {
        /** @var FinancingApplicationByScaninaRequestDto $dto */
        $userCoreResponse = $this->coreClient->findCustomerById($dto->profileXid);
        // $financingApplicationEloquentModel = $this->financingApplicationRepository->add([
        //     'xid' => nano_id(),
        //     'application_code' => $this->generateApplicationCode(),
        //     'profile_xid' => $dto->profileXid,
        //     'profile_snapshot' => $profileSnapshot,
        //     'facility_id' => config(),
        //     'method_id' => config(),
        //     'financing_objects' => $financingObjects,
        //     'is_receive_offer' => true,
        //     'segment' => $dto->segment,
        //     'project_location' => $dto->projectLocation,
        //     'status_id' => FinancingStatusEnum::PROCESSED,
        //     'type_id' => FinancingApplicationTypeEnum::PERSONAL,
        // ]);
        //
        return (object) [
            'xid' => nano_id(),
            'application_code' => $this->generateApplicationCode(),
            'status_id' => FinancingStatusEnum::PROCESSED,
            'status_name' => 'Diproses',
            'financing_object_count' => count($dto->objects),
            'financing_facility_name' => 'Investasi',
            'financing_method_name' => 'Sewa Pembiayaan',
            'created_at' => CarbonImmutable::now(),
        ];
    }

    protected function generateApplicationCode()
    {
        $now = CarbonImmutable::now();
        $year = $now->year;
        $month = $now->month;
        $count = $this->financingApplicationRepository->size($this->financingSpecificationFactory->findByMonth($now));
        $width = 6;
        if ($count >= 999999) {
            throw new FinancingApplicationLimitExceedException();
        }

        return "{$month}{$year}" . str_pad((string) $count++, $width, '0', STR_PAD_LEFT);
    }
}
