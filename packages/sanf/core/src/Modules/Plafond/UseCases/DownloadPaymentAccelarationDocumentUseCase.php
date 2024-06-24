<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Exceptions\PaymentAccDocumentNotFoundException;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;

final class DownloadPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $paymentAccDocumentRepositoryInterface;

    public function __construct(PaymentAccelarationDocumentRepositoryInterface $paymentAccDocumentRepositoryInterface)
    {
        $this->paymentAccDocumentRepositoryInterface = $paymentAccDocumentRepositoryInterface;
    }

    public function execute($dto = null)
    {
        $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->findByPlafondId($dto->plafondId);

        if (is_null($paymentAccDocumentEloquent)) {
            throw new PaymentAccDocumentNotFoundException();
        }

        return $this->generateFile([
            'company' => $paymentAccDocumentEloquent->company ?? 'NO NAME',
            'bowheer' => $paymentAccDocumentEloquent->bowheer ?? 'NO NAME',
            'document_no' => $paymentAccDocumentEloquent->document_no,
            'document_date' => Carbon::parse($paymentAccDocumentEloquent->document_date)->locale('id_ID')->isoFormat('DD MMMM YYYY'),
            'first_signer_company' => $paymentAccDocumentEloquent->first_signer_company ?? $paymentAccDocumentEloquent->company,
            'first_signer_name' => $paymentAccDocumentEloquent->first_signer_name,
            'first_signer_position' => $paymentAccDocumentEloquent->first_signer_position,
            'second_signer_company' => $paymentAccDocumentEloquent->second_signer_company ?? 'NO NAME',
            'second_signer_name' => $paymentAccDocumentEloquent->second_signer_name,
            'second_signer_position' => $paymentAccDocumentEloquent->second_signer_position,
        ]);
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
