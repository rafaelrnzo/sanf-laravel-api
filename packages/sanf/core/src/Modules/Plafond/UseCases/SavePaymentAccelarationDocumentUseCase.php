<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;

final class SavePaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $paymentAccDocumentRepositoryInterface;

    public function __construct(PaymentAccelarationDocumentRepositoryInterface $paymentAccDocumentRepositoryInterface)
    {
        $this->paymentAccDocumentRepositoryInterface = $paymentAccDocumentRepositoryInterface;
    }

    public function execute($dto = null)
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $this->generateFile([
            'company' => $dto->companyName,
            'bowheer' => $dto->bowheerName ?? 'NO NAME',
            'document_no' => $dto->documentNo,
            'document_date' => Carbon::parse($dto->documentDate)->locale('id_ID')->isoFormat('DD MMMM YYYY'),
            'first_signer_company' => $dto->firstSigner->company ?? $dto->companyName,
            'first_signer_name' => $dto->firstSigner->fullName,
            'first_signer_position' => $dto->firstSigner->position,
            'second_signer_company' => $dto->secondSigner->company ?? 'NO NAME',
            'second_signer_name' => $dto->secondSigner->fullName,
            'second_signer_position' => $dto->secondSigner->position,

        ]));
        $directory = config('image-path.payment-acc-document');
        $filename = file_upload($tempFilePath, $directory, 'public');

        $fileExist = Storage::exists("{$directory}{$filename}");
        if (!$fileExist) {
            throw new FileNotFoundException("{$directory}{$filename}");
        }

        $metadata = Storage::getMetaData("{$directory}{$filename}");
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
        ];

        $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->findByPlafondId($dto->plafondId);
        if (is_null($paymentAccDocumentEloquent)) {
            $paymentAccDocumentData['xid'] = nano_id();
            $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->create($paymentAccDocumentData);
        } else {
            $this->paymentAccDocumentRepositoryInterface->update($paymentAccDocumentEloquent->id, $paymentAccDocumentData);
        }

        return Storage::download($paymentAccDocumentEloquent->path, $paymentAccDocumentEloquent->origin);
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
