<?php

namespace Sanf\Core\Modules\Financing\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationObjectByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Enums\FinancingApplicationTypeEnum;
use Sanf\Core\Modules\Financing\Enums\FinancingStatusEnum;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationLimitExceedException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class SubmitFinanceApplicationByScaninaUseCase implements ApplicationServiceInterface
{
    private const SCANINA_CLIENT = 'SCAN';

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

        $profile = collect($userCoreResponse['data'])
            ->where('ID_IDENTITY', ProfileType::PERSONAL)
            ->map(function ($item) {
                return (object) [
                    'xid' => $item['CUST_ID_SANF'],
                    'typeId' => $item['ID_IDENTITY'],
                    'typeName' => $item['DESC_IDENTITY'],
                    'title' => $item['COMPANY_TYPE'],
                    'fullName' => $item['IDENTITY_NAME'],
                    'picName' => $item['PIC_NAME'],
                    'identityNumber' => $item['KTP'],
                    'npwp' => $item['NPWP'],
                    'email' => $item['EMAIL_ADDR'],
                    'landlineNumber' => $item['NO_TELP'],
                    'phoneNumber' => $item['NO_HP'],
                    'gender' => $item['GENDER'],
                    'birthdate' => $item['TGL_LAHIR'],
                    'countryId' => $item['ID_NEGARA'],
                    'countryName' => $item['NEGARA'],
                    'provinceId' => $item['ID_PROVINSI'],
                    'provinceName' => $item['PROVINSI'],
                    'cityId' => $item['ID_KOTA'],
                    'cityName' => $item['KOTA'],
                    'districtName' => $item['KECAMATAN'],
                    'subdistrictName' => $item['KELURAHAN'],
                    'postcode' => $item['KODEPOS'],
                    'address' => $item['ALAMAT'],
                    'businessSince' => $item['LAMA_USAHA'],
                    'isPic' => (bool) $item['PIC'],
                ];
            })->first();

        $applicationCode = $this->generateApplicationCode();
        $financingApplicationEloquentModel = $this->financingApplicationRepository->add([
            'xid' => nano_id(),
            'application_code' => $applicationCode,
            'profile_xid' => $dto->profileXid,
            'profile_snapshot' => $profile,
            'facility_id' => config('scanina-web.financing-application.facility'),
            'method_id' => config('scanina-web.financing-application.method'),
            'financing_objects' => array_map(function ($object) {
                /* @var FinancingApplicationObjectByScaninaRequestDto $object */
                return [
                    'amount' => $object->quantity,
                    'provider_name' => $object->provider,
                    'category_name' => $object->category,
                    'brand_name' => $object->brand,
                    'type_name' => $object->type,
                    'model_name' => $object->model,
                    'description' => $object->description,
                    'price_per_unit' => $object->pricePerUnit,
                    'client' => self::SCANINA_CLIENT,
                ];
            }, $dto->objects),
            'financing_history' => [
                'status_id' => FinancingStatusEnum::PROCESSED,
                'facility_id' => config('scanina-web.financing-application.facility'),
                'method_id' => config('scanina-web.financing-application.method'),
                'amount' => $dto->payment->amount,
                'down_payment_amount' => $dto->payment->downPaymentAmount,
                'down_payment_percentage' => $dto->payment->downPaymentPercentage,
                'tax_amount' => $dto->payment->taxAmount,
                'vat_amount' => $dto->payment->vatAmount,
                'backharge_amount' => $dto->payment->backhargeAmount,
                'other_amount' => $dto->payment->otherAmount,
                'total_amount' => $dto->payment->totalAmount,
                'tenor' => $dto->payment->tenor,
                'request_snapshot' => json_encode($dto),
                'client' => self::SCANINA_CLIENT,
                'created_by' => json_encode($profile),
            ],
            'is_receive_offer' => $dto->isReceiveOffer,
            'status_id' => FinancingStatusEnum::PROCESSED,
            'type_id' => FinancingApplicationTypeEnum::PERSONAL,
            'total_object' => count($dto->objects),
            'client' => self::SCANINA_CLIENT,
        ]);

        return (object) [
            'xid' => $financingApplicationEloquentModel->xid,
            'application_code' => $financingApplicationEloquentModel->application_code,
            'status_id' => $financingApplicationEloquentModel->status_id,
            'status_name' => (new FinancingStatusEnum($financingApplicationEloquentModel->status_id))->getValue(),
            'financing_object_count' => count($dto->objects),
            'financing_facility_name' => 'Investasi',
            'financing_method_name' => 'Sewa Pembiayaan',
            'created_at' => $financingApplicationEloquentModel->created_at,
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

        return self::SCANINA_CLIENT . "{$month}{$year}" . str_pad((string) $count++, $width, '0', STR_PAD_LEFT);
    }
}
