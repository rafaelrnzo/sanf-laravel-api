<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use Carbon\CarbonImmutable;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;

class StandbyFinancingResponseService
{
    public function listItem(StandbyFinancingApplicationModel $application, int $rowNumber): array
    {
        return [
            'rn' => (string) $rowNumber,
            'recap_id_b2b' => $application->recap_id_b2b,
            'period_start' => $this->dateTime($application->period_start),
            'period_end' => $this->dateTime($application->period_end),
            'date_recap' => $this->dateTime($application->created_at),
            'total_invoice' => (string) $application->total_invoice,
            'total_amount' => $this->amount($application->total_amount),
            'state_code' => (string) $application->state_code,
        ];
    }

    public function detail(StandbyFinancingApplicationModel $application): array
    {
        $invoices = $application->invoices->values()->map(function ($invoice, $index) {
            return [
                'recap_id' => $invoice->recap_id,
                'nomor_invoice' => $invoice->invoice_number,
                'tanggal_invoice' => $this->dateTime($invoice->invoice_date),
                'currency' => $invoice->currency,
                'amount' => $this->amount($invoice->amount),
            ];
        })->all();

        return [
            'recap_id_b2b' => $application->recap_id_b2b,
            'b2b_id' => $application->cust_id,
            'period_start' => $this->dateTime($application->period_start),
            'period_end' => $this->dateTime($application->period_end),
            'date_recap' => $this->dateTime($application->created_at),
            'total_invoice' => (string) $application->total_invoice,
            'total_amount' => $this->amount($application->total_amount),
            'state_code' => (string) $application->state_code,
            'suppliers' => [
                [
                    'supplier_id' => $application->supplier_id,
                    'supplier_name' => $application->supplier_name,
                    'total_invoice' => (string) $application->total_invoice,
                    'total_amount' => $this->amount($application->total_amount),
                    'invoice_list' => $invoices,
                ],
            ],
            'bank' => [
                'bank_id' => $application->bankAccount->bank_id ?? null,
                'owner' => $application->bankAccount->owner ?? null,
                'provider' => $application->bankAccount->provider ?? null,
                'account_number' => $application->bankAccount->account_number ?? null,
                'total_amount' => isset($application->bankAccount) ? $this->amount($application->bankAccount->total_amount) : null,
            ],
            'documents' => $application->documents->map(function ($document) {
                return [
                    'doc_id' => $document->doc_id,
                    'file_name' => $document->file_name,
                ];
            })->all(),
        ];
    }

    private function dateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return CarbonImmutable::parse($value)->format('Y-m-d H:i:s');
    }

    private function amount($value): string
    {
        $amount = (float) $value;

        if (floor($amount) === $amount) {
            return (string) (int) $amount;
        }

        return (string) $amount;
    }
}
