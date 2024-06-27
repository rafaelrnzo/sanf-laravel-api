<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;

final class DownloadPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        return $this->generateFile([
            'company' => 'PIHAK PERTAMA (PT)',
            'bowheer' => 'PIHAK KEDUA (PT)',
            'document_no' => 'NOMOR SURAT',
            'document_date' => 'TANGGAL SURAT',
            'first_signer_company' => 'PIHAK PERTAMA (PT)',
            'first_signer_name' => 'PEJABAT PIHAK PERTAMA',
            'first_signer_position' => 'JABATAN PIHAK PERTAMA',
            'second_signer_company' => 'PIHAK KEDUA (PT)',
            'second_signer_name' => 'PEJABAT PIHAK KEDUA',
            'second_signer_position' => 'JABATAN PIHAK KEDUA',
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
