<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use Carbon\CarbonImmutable;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\StandbyFinancing\Exceptions\StandbyFinancingValidationException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class StandbyFinancingPlafondService
{
    private SanfCoreApiClient $coreClient;

    public function __construct(SanfCoreApiClient $coreClient)
    {
        $this->coreClient = $coreClient;
    }

    public function header(string $customerId): array
    {
        return $this->coreClient->getCustomerPlafonds($customerId);
    }

    public function listSbf(string $customerId): array
    {
        $response = $this->coreClient->getPlafondFactoringV2($customerId, PlafondTypeEnum::SBF);
        $items = $response['data'] ?? [];

        if (empty($items)) {
            $header = $this->coreClient->getCustomerPlafonds($customerId);
            $items = array_values(array_filter($header['data'] ?? [], function ($item) {
                return ($item['P_CODE'] ?? $item['p_code'] ?? null) === PlafondTypeEnum::SBF;
            }));
        }

        return array_map(function ($item) use ($customerId) {
            return $this->mapPlafond((array) $item, $customerId);
        }, $items);
    }

    public function detailSbf(string $customerId, string $noPlafond): array
    {
        foreach ($this->listSbf($customerId) as $plafond) {
            if ($plafond['no_plafond'] === $noPlafond) {
                return $plafond;
            }
        }

        throw new StandbyFinancingValidationException('Plafond SBF not found.');
    }

    public function validateActiveSbf(string $customerId, string $noPlafond): array
    {
        $plafond = $this->detailSbf($customerId, $noPlafond);

        if ($plafond['p_code'] !== PlafondTypeEnum::SBF) {
            throw new StandbyFinancingValidationException('Selected plafond is not SBF.');
        }

        if (!empty($plafond['exp_date']) && CarbonImmutable::parse($plafond['exp_date'])->lt(CarbonImmutable::today())) {
            throw new StandbyFinancingValidationException('Selected plafond is expired.');
        }

        if ((float) $plafond['p_sisa'] <= 0) {
            throw new StandbyFinancingValidationException('Selected plafond has no remaining balance.');
        }

        return $plafond;
    }

    public function findSupplier(array $plafond, string $supplierId): array
    {
        foreach ($plafond['supplier'] as $supplier) {
            if (($supplier['supplier_id'] ?? null) === $supplierId) {
                return $supplier;
            }
        }

        throw new StandbyFinancingValidationException('Supplier is not registered under selected plafond.');
    }

    private function mapPlafond(array $item, string $customerId): array
    {
        $suppliers = $item['supplier'] ?? $item['SUPPLIER'] ?? $item['suppliers'] ?? $item['BOWHEER'] ?? [];
        if (isset($suppliers['supplier_id']) || isset($suppliers['SUPPLIER_ID']) || isset($suppliers['BOWHEER_ID'])) {
            $suppliers = [$suppliers];
        }

        return [
            'no_plafond' => (string) ($item['no_plafond'] ?? $item['NO_PLAFOND'] ?? $item['noplafond'] ?? $item['PLAFONDHEADER_ID'] ?? ''),
            'cust_id' => (string) ($item['cust_id'] ?? $item['CUST_ID'] ?? $customerId),
            'p_code' => (string) ($item['p_code'] ?? $item['P_CODE'] ?? PlafondTypeEnum::SBF),
            'p_total' => (string) ($item['p_total'] ?? $item['P_TOTAL'] ?? $item['P_CURRENT'] ?? '0'),
            'p_terpakai' => (string) ($item['p_terpakai'] ?? $item['P_TERPAKAI'] ?? $item['P_USED'] ?? '0'),
            'p_invoice_ongoing' => (string) ($item['p_invoice_ongoing'] ?? $item['P_INVOICE_ONGOING'] ?? '0'),
            'p_sisa' => (string) ($item['p_sisa'] ?? $item['P_SISA'] ?? '0'),
            'exp_date' => (string) ($item['exp_date'] ?? $item['EXP_DATE'] ?? ''),
            'supplier' => array_map([$this, 'mapSupplier'], $suppliers),
        ];
    }

    private function mapSupplier($supplier): array
    {
        $supplier = (array) $supplier;

        return [
            'reg_no' => (string) ($supplier['reg_no'] ?? $supplier['REG_NO'] ?? ''),
            'supplier_id' => (string) ($supplier['supplier_id'] ?? $supplier['SUPPLIER_ID'] ?? $supplier['BOWHEER_ID'] ?? $supplier['CUST_ID'] ?? ''),
            'nama_supplier' => (string) ($supplier['nama_supplier'] ?? $supplier['SUPPLIER_NAME'] ?? $supplier['BOWHEER_NAME'] ?? $supplier['IDENTITY_NAME'] ?? ''),
            'masa_aktif' => (string) ($supplier['masa_aktif'] ?? $supplier['MASA_AKTIF'] ?? ''),
            'tenor_pencairan_min' => (string) ($supplier['tenor_pencairan_min'] ?? $supplier['TENOR_PENCAIRAN_MIN'] ?? '1'),
            'tenor_pencairan_max' => (string) ($supplier['tenor_pencairan_max'] ?? $supplier['TENOR_PENCAIRAN_MAX'] ?? '90'),
            'tipe_tenor' => (string) ($supplier['tipe_tenor'] ?? $supplier['TIPE_TENOR'] ?? 'Daily'),
            'curr_id' => (string) ($supplier['curr_id'] ?? $supplier['CURR_ID'] ?? 'IDR'),
        ];
    }
}
