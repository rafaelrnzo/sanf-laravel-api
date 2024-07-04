<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class SavePaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $paymentAccDocumentRepositoryInterface;
    private $coreClient;

    public function __construct(
        PaymentAccelarationDocumentRepositoryInterface $paymentAccDocumentRepositoryInterface,
        SanfCoreApiClient $coreClient
    ) {
        $this->paymentAccDocumentRepositoryInterface = $paymentAccDocumentRepositoryInterface;
        $this->coreClient = $coreClient;
    }

    public function execute($dto = null)
    {
        $coreResponse = $this->coreClient->getBowheer($dto->clientId, null, null, 1000, 1);
        $bowheerCoreData = collect($coreResponse['data']) ?? null;
        $bowheerCoreDataByName = $bowheerCoreData->where('BOWHEER_NAME', '=', $dto->bowheerName)->first();

        $invoices = [];
        $totalAmount = 0;
        foreach ($dto->invoices as $index => $invoice) {
            $counter = $index + 1;
            $invoices[] = [
                'index' => "{$counter}.",
                'no' => $invoice->invoiceNo ?? 'xxxx-xxxx/xxxx/xxxx',
                'date' => ($invoice->invoiceDate) ? Carbon::parse($invoice->invoiceDate)->format('d/m/Y') : 'dd/mm/yyyy',
                'amount' => ($invoice->invoiceAmount) ? 'Rp ' . number_format($invoice->invoiceAmount, 2, ',', '.') : 'Rp 0,00',
                'vat_amount' => ($invoice->vatAmount) ? 'Rp ' . number_format($invoice->vatAmount, 2, ',', '.') : 'Rp 0,00',
                'tax_amount' => ($invoice->taxAmount) ? 'Rp ' . number_format($invoice->taxAmount, 2, ',', '.') : 'Rp 0,00',
                'backharge_amount' => ($invoice->backhargeAmount) ? 'Rp ' . number_format($invoice->backhargeAmount, 2, ',', '.') : 'Rp 0,00',
                'total_amount' => ($invoice->totalAmount) ? 'Rp ' . number_format($invoice->totalAmount, 2, ',', '.') : 'Rp 0,00',
            ];
            $totalAmount += $invoice->totalAmount;
        }
        $pdfFile = $this->generateFile([
            'client' => $dto->companyName ?? '(PT) Pihak Pertama',
            'customer' => $dto->bowheerName ?? '(PT) Pihak Kedua',
            'customer_address' => $bowheerCoreDataByName['BOWHEER_ADDRESS'] ?? 'Jl. Alamat Pihak Kedua',
            'document_no' => $dto->documentNo ?? 'Nomor Surat',
            'document_date' => ($dto->documentDate) ? Carbon::parse($dto->documentDate)->locale('id_ID')->isoFormat('DD MMMM YYYY') : 'Tanggal Surat',
            'first_signer_company' => $dto->firstSigner->company ?? '(PT) Pihak Pertama',
            'first_signer_name' => $dto->firstSigner->fullName ?? 'Pejabat Pihak Pertama',
            'first_signer_position' => $dto->firstSigner->position ?? 'Jabatan Pihak Pertama',
            'second_signer_company' => $dto->secondSigner->company ?? '(PT) Pihak Kedua',
            'second_signer_name' => $dto->secondSigner->fullName ?? 'Pejabat Pihak Kedua',
            'second_signer_position' => $dto->secondSigner->position ?? 'Jabatan Pihak Kedua',
            'invoices' => $invoices,
            'total_amount' => 'Rp ' . number_format($totalAmount, 2, ',', '.'),
        ]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $pdfFile);
        $directory = config('image-path.payment-acc-document');
        $filename = file_upload($tempFilePath, $directory, 'public');

        $fileExist = Storage::disk('minio_post')->exists("{$directory}{$filename}");
        if (!$fileExist) {
            throw new FileNotFoundException("{$directory}{$filename}");
        }

        $metadata = Storage::disk('minio_post')->getMetaData("{$directory}{$filename}");
        $fileMetadata = [
            'file_name' => $filename,
            'directory' => $directory,
            'path' => "{$directory}{$filename}",
            'mime_type' => $metadata['mimetype'],
            'size' => $metadata['size'],
        ];

        $paymentAccDocumentData = [
            'client_id' => $dto->clientId,
            'plafond_id' => $dto->plafondId,
            'company' => $dto->companyName,
            'bowheer' => $dto->bowheerName ?? '',
            'bowheer_email' => $dto->bowheerEmail ?? '',
            'document_no' => $dto->documentNo,
            'document_date' => $dto->documentDate,
            'first_signer_company' => $dto->firstSigner->company ?? $dto->companyName,
            'first_signer_name' => $dto->firstSigner->fullName,
            'first_signer_position' => $dto->firstSigner->position,
            'second_signer_company' => $dto->secondSigner->company ?? 'NO NAME',
            'second_signer_name' => $dto->secondSigner->fullName,
            'second_signer_position' => $dto->secondSigner->position,
            'origin' => "surat-percepatan-plafond-{$dto->plafondId}.pdf",
            'filename' => $filename,
            'path' => "{$directory}/{$filename}",
            'metadata' => json_encode($fileMetadata),
            'invoices' => json_encode($dto->invoices),
            'bowheer_address' => $bowheerCoreDataByName['BOWHEER_ADDRESS'] ?? null,
        ];

        $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->findByPlafondId($dto->plafondId);
        if (is_null($paymentAccDocumentEloquent)) {
            $paymentAccDocumentData['xid'] = nano_id();
            $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->create($paymentAccDocumentData);
        } else {
            $this->paymentAccDocumentRepositoryInterface->update($paymentAccDocumentEloquent->id, $paymentAccDocumentData);
        }

        return $pdfFile;
    }

    private function generateFile(array $content)
    {
        $template = view('acc-document-template', ['content' => $content])->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($template);
        $pdf->setPaper('A4', 'potrait');
        $pdf->render();

        return $pdf->output();
    }
}
