<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Client;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreContractDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailOverdueEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentSummaryEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCorePlafondSparePartEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementEntity;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCorePayInstallmentPayload;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreSubmitSparePartFinancingPayload;
use Sanf\Integration\Responses\SanfCoreV2ListResponse;

class SanfCoreApiClientV2
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';
    public const DEFAULT_TIMEZONE = 'Asia/Jakarta';

    protected $client;

    public function __construct()
    {
        $verifyOnProduction = config('app.env') === 'production';

        $this->client = new Client([
            'verify' => $verifyOnProduction,
        ]);
    }

    // Spare Part Disbursement / Spare Part Financing ============

    public function getSparePartDisbursementList(
        $page,
        $per_page
    ) {
        $response = Request::route('sanf-internal-v2.spare-part-disbursement.list', $this->client)
            ->queryParams([
                'page' => $page,
                'per_page' => $per_page,
                'request_type' => 'mobile',
            ])
            ->send();

        $jsonResponse = $response->json();

        $jsonResponse['data'] = array_map(
            fn ($item) => new SanfCoreSparePartDisbursementEntity($item),
            $jsonResponse['data']
        );

        return new SanfCoreV2ListResponse($jsonResponse);
    }

    public function getSparePartDisbursementDetail(
        $batchId,
        $customerId
    ): ?SanfCoreSparePartDisbursementDetailEntity {
        try {
            $response = Request::route('sanf-internal-v2.spare-part-disbursement.detail', $this->client)
                ->pathParams([
                    'batchId' => $batchId,
                    'customerId' => $customerId,
                ])
                ->queryParams([
                    'request_type' => 'mobile',
                ])
                ->send();

            $jsonResponse = $response->json();

            return new SanfCoreSparePartDisbursementDetailEntity($jsonResponse['data']);
        } catch (SanfInternalApiDataNotFoundException $e) {
            return null;
        }
    }

    public function submitSparePartFinancing(SanfCoreSubmitSparePartFinancingPayload $payload)
    {
        $response = Request::route('sanf-internal-v2.spare-part-disbursement.submit', $this->client)
            ->json($payload->toArray())
            ->send();

        return $response->json();
    }

    // Contract =============================

    public function getContractDetail(string $contractNumber): ?SanfCoreContractDetailEntity
    {
        try {
            $response = Request::route('sanf-internal-v2.contract.detail', $this->client)
                ->pathParams(['contractNumber' => $contractNumber])
                ->send();

            $jsonResponse = $response->json();

            return SanfCoreContractDetailEntity::fromLowercaseKeys($jsonResponse['data']);
        } catch (SanfInternalApiDataNotFoundException $e) {
            return null;
        }
    }

    // Plafond ===============================

    public function getPlafondSparePartList(
        $page,
        $per_page
    ) {
        $response = Request::route('sanf-internal-v2.plafond.spare-part.list', $this->client)
            ->queryParams([
                'page' => $page,
                'per_page' => $per_page,
            ])
            ->send();

        $jsonResponse = $response->json();

        $jsonResponse['data'] = array_map(
            fn ($item) => new SanfCorePlafondSparePartEntity($item),
            $jsonResponse['data']
        );

        return new SanfCoreV2ListResponse($jsonResponse);
    }

    // Installment =========================

    public function getInstallmentSummary(): ?SanfCoreInstallmentSummaryEntity
    {
        try {
            $response = Request::route('sanf-internal-v2.installment.summary', $this->client)->send();

            $jsonResponse = $response->json();

            return new SanfCoreInstallmentSummaryEntity($jsonResponse['data']);
        } catch (SanfInternalApiDataNotFoundException $e) {
            return null;
        }
    }

    public function getInstallmentList(
        $page,
        $per_page,
        $type = 'current_month',
        $sort_by = null,
        $status = null
    ) {
        $response = Request::route('sanf-internal-v2.installment.list', $this->client)
            ->queryParams([
                'page' => $page,
                'per_page' => $per_page,
                'type' => $type, // current_month | next_month
                'sort_by' => $sort_by,
                'status' => $status,
            ])
            ->send();

        $jsonResponse = $response->json();

        $jsonResponse['data'] = array_map(
            fn ($item) => new SanfCoreInstallmentEntity($item),
            $jsonResponse['data']
        );

        return new SanfCoreV2ListResponse($jsonResponse);
    }

    /**
     * @param string $no_kontrak
     * @param string $jatuh_tempo 'Y-m-d' format
     * @return SanfCoreInstallmentDetailEntity|null
     */
    public function getInstallmentDetail(string $no_kontrak, string $jatuh_tempo): ?SanfCoreInstallmentDetailEntity
    {
        try {
            $response = Request::route('sanf-internal-v2.installment.detail', $this->client)
                ->pathParams(compact('no_kontrak', 'jatuh_tempo'))
                ->send();

            $jsonResponse = $response->json();

            if (!empty($jsonResponse['data']['overdue'])) {
                $jsonResponse['data']['overdue'] = array_map(
                    fn ($item) => new SanfCoreInstallmentDetailOverdueEntity($item),
                    $jsonResponse['data']['overdue']
                );
            }

            return new SanfCoreInstallmentDetailEntity($jsonResponse['data']);
        } catch (SanfInternalApiDataNotFoundException $e) {
            return null;
        }
    }

    public function payInstallment(SanfCorePayInstallmentPayload $payload)
    {
        $response = Request::route('sanf-internal-v2.installment.pay', $this->client)
            ->json($payload->toArray())
            ->send();

        return $response->json();
    }
}
