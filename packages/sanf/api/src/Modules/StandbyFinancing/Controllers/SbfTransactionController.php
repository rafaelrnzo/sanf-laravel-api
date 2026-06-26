<?php

namespace Sanf\Api\Modules\StandbyFinancing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfInvoiceCheckModel;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SbfTransactionController extends RestApiController
{
    public function checkInvoice(Request $request, SanfApiService $service): JsonResponse
    {
        $payload = $this->validate($request, [
            'cust_id' => ['required', 'string', 'max:50'],
            'no_plafond' => ['required', 'string', 'max:50'],
            'nomor_invoice' => ['required', 'string', 'max:100'],
            'total_invoice' => ['required', 'integer', 'min:1'],
        ]);

        $service->setUser($payload['cust_id']);

        $record = SbfInvoiceCheckModel::create($payload + [
            'core_status' => 'pending',
        ]);

        try {
            $coreResponse = $service->checkInvoice($payload);
            $record->update([
                'core_status' => 'success',
                'core_message' => data_get($coreResponse, 'message'),
                'core_response' => $coreResponse,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $coreResponse,
            ]);
        } catch (\Throwable $exception) {
            $record->update([
                'core_status' => 'error',
                'core_message' => $exception->getMessage(),
            ]);

            Log::error('SBF invoice check failed', [
                'record_id' => $record->id,
                'message' => $exception->getMessage(),
            ]);

            return $this->coreUnavailable();
        }
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $this->validate($request, [
            'cust_id' => ['required', 'string', 'max:50'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('file');

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';
        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
        $dir = 'uploads/sbf/' . date('Y/m');

        $path = Storage::disk('minio_post')->putFileAs($dir, $file, $fileName);

        if ($path === false) {
            Log::error('SBF document upload failed', [
                'cust_id' => $request->input('cust_id'),
                'origin_name' => $file->getClientOriginalName(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah dokumen',
            ], 502);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'xid' => 'SBF-' . uniqid(),
                'file_name' => $fileName,
                'path' => $path,
                'origin_name' => $file->getClientOriginalName(),
            ],
        ]);
    }

    public function submitPengajuan(Request $request, SanfApiService $service): JsonResponse
    {
        $payload = $this->validate($request, [
            'cust_id' => ['required', 'string', 'max:50'],
            'no_plafond' => ['required', 'string', 'max:50'],
            'period_start' => ['required', 'date', 'before:period_end'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'tenor' => ['required', 'integer', 'min:1', 'max:90'],
            'supplier' => ['required', 'array', 'min:1'],
            'supplier.*.supplier_id' => ['required', 'string'],
            'supplier.*.total_invoice' => ['required', 'integer', 'min:1'],
            'supplier.*.total_amount' => ['required', 'integer', 'min:1'],
            'supplier.*.invoice_list' => ['required', 'array', 'min:1'],
            'supplier.*.invoice_list.*.nomor_invoice' => ['required', 'string'],
            'supplier.*.invoice_list.*.tanggal_invoice' => ['required', 'date'],
            'supplier.*.invoice_list.*.currency' => ['required', 'in:IDR'],
            'supplier.*.invoice_list.*.amount' => ['required', 'integer', 'min:1'],
            'bank_account' => ['required', 'array'],
            'bank_account.bank_id' => ['required', 'string', 'max:20'],
            'bank_account.bank_owner' => ['required', 'string', 'max:100'],
            'bank_account.bank_provider' => ['required', 'string', 'max:100'],
            'bank_account.bank_account_number' => ['required', 'string', 'max:50'],
            'invoice_document' => ['required', 'array', 'min:1'],
            'spt_dokuments' => ['required', 'array'],
            'supporting_dokuments' => ['nullable', 'array'],
        ]);

        $service->setUser($payload['cust_id']);

        $totalInvoiceCount = array_sum(array_column($payload['supplier'], 'total_invoice'));
        $totalAmount = array_sum(array_column($payload['supplier'], 'total_amount'));
        $bankAccount = $payload['bank_account'];

        $record = SbfPengajuanModel::create([
            'cust_id' => $payload['cust_id'],
            'no_plafond' => $payload['no_plafond'],
            'period_start' => $payload['period_start'],
            'period_end' => $payload['period_end'],
            'tenor' => $payload['tenor'],
            'total_invoice_count' => $totalInvoiceCount,
            'total_amount' => $totalAmount,
            'bank_id' => $bankAccount['bank_id'],
            'bank_owner' => $bankAccount['bank_owner'],
            'bank_provider' => $bankAccount['bank_provider'],
            'bank_account_number' => $bankAccount['bank_account_number'],
            'supplier_payload' => $payload['supplier'],
            'invoice_document' => $payload['invoice_document'],
            'spt_dokument' => $payload['spt_dokuments'],
            'supporting_dokuments' => $payload['supporting_dokuments'] ?? null,
            'local_status' => 'pending',
            'core_status' => 'pending',
        ]);

        try {
            $coreResponse = $service->submitPengajuan($payload);
            $record->update([
                'local_status' => 'submitted',
                'core_recap_id' => $this->recapId($coreResponse),
                'core_status' => 'success',
                'core_message' => data_get($coreResponse, 'message'),
                'core_response' => $coreResponse,
                'submitted_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $coreResponse,
            ]);
        } catch (\Throwable $exception) {
            $record->update([
                'local_status' => 'failed',
                'core_status' => 'error',
                'core_message' => $exception->getMessage(),
            ]);

            Log::error('SBF pengajuan failed', [
                'record_id' => $record->id,
                'message' => $exception->getMessage(),
            ]);

            return $this->coreUnavailable();
        }
    }

    private function recapId(array $response): ?string
    {
        $recapId = data_get($response, 'data.recap_id_b2b')
            ?? data_get($response, 'data.recap_id')
            ?? data_get($response, 'recap_id_b2b')
            ?? data_get($response, 'recap_id');

        return $recapId === null ? null : (string) $recapId;
    }

    private function coreUnavailable(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghubungi core system',
        ], 502);
    }
}
