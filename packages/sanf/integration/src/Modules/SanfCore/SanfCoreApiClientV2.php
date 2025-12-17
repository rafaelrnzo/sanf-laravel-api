<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Client;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreContractDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentSummaryEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCorePlafondSparePartEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementEntity;
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
                ->send();

            $jsonResponse = $response->json();

            return new SanfCoreSparePartDisbursementDetailEntity($jsonResponse['data']);
        } catch (SanfInternalApiDataNotFoundException $e) {
            return null;
        }
    }

    public function submitSparePartFinancing(SanfCoreSubmitSparePartFinancingPayload $payload)
    {
        $response = Request::route('spare-part-disbursement.list', $this->client)
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
}
