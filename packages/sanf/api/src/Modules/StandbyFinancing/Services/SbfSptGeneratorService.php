<?php

namespace Sanf\Api\Modules\StandbyFinancing\Services;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Validation\ValidationException;

class SbfSptGeneratorService
{
    public function render(array $payload): string
    {
        $content = $this->prepareTemplateContent($payload);

        $html = view('api::pdf.spt', $content)->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->output();
    }

    public function fileName(array $payload): string
    {
        $safeNumber = rtrim(strtr(base64_encode((string) ($payload['letter_number'] ?? '')), '+/', '-_'), '=');
        $date = (string) ($payload['letter_date'] ?? '');

        return "SPT__{$safeNumber}__{$date}__Generated.pdf";
    }

    private function prepareTemplateContent(array $payload): array
    {
        $invoiceList = $this->flattenInvoiceList($payload['supplier'] ?? []);

        if (empty($invoiceList)) {
            throw ValidationException::withMessages([
                'supplier' => 'Daftar Invoice atau Purchase Order tidak boleh kosong.',
            ]);
        }

        $totalPoAmount = array_sum(array_column($invoiceList, 'amount'));

        $totalDisbursement = isset($payload['total_disbursement'])
            ? (int) $payload['total_disbursement']
            : (int) array_sum(array_column($payload['supplier'] ?? [], 'total_amount'));

        if ($totalDisbursement <= 0) {
            $totalDisbursement = $totalPoAmount;
        }

        if ($totalDisbursement > $totalPoAmount) {
            throw ValidationException::withMessages([
                'total_disbursement' => 'Total nominal pencairan tidak boleh melebihi total nominal PO/Invoice.',
            ]);
        }

        $bank = $payload['bank_account'] ?? [];

        return [
            'letter_number' => (string) ($payload['letter_number'] ?? ''),
            'letter_date' => Carbon::parse($payload['letter_date'] ?? Carbon::now('Asia/Jakarta'))->locale('id'),
            'signer_name' => (string) ($payload['signer_name'] ?? ''),
            'signer_role' => (string) ($payload['signer_role'] ?? ''),
            'plafond_number' => (string) ($payload['no_plafond'] ?? ''),
            'customer' => [
                'identity_name' => $payload['customer']['identity_name'] ?? null,
                'address' => $payload['customer']['address'] ?? null,
                'phone' => $payload['customer']['phone'] ?? null,
                'email' => $payload['customer']['email'] ?? null,
            ],
            'bank_account' => [
                'provider' => $bank['bank_provider'] ?? $bank['provider'] ?? '',
                'account_number' => $bank['bank_account_number'] ?? $bank['account_number'] ?? '',
                'owner' => $bank['bank_owner'] ?? $bank['owner'] ?? '',
            ],
            'invoice_list' => $invoiceList,
            'total_po_amount' => $totalPoAmount,
            'total_disbursement' => $totalDisbursement,
        ];
    }

    private function flattenInvoiceList(array $suppliers): array
    {
        $rows = [];

        foreach ($suppliers as $supplier) {
            foreach (($supplier['invoice_list'] ?? []) as $invoice) {
                if (empty($invoice['nomor_invoice'])) {
                    continue;
                }

                $rows[] = [
                    'nomor' => (string) $invoice['nomor_invoice'],
                    'tanggal' => $invoice['tanggal_invoice'] ?? '',
                    'amount' => (int) ($invoice['amount'] ?? 0),
                ];
            }
        }

        return $rows;
    }
}
