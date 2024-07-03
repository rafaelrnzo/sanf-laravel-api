<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;

final class DownloadPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        return $this->generateFile([
            'client' => '(PT) Pihak Pertama',
            'customer' => '(PT) Pihak Kedua',
            'customer_address' => 'Jl. Alamat Pihak Kedua',
            'document_no' => 'Nomor Surat',
            'document_date' => 'Tanggal Surat',
            'first_signer_company' => '(PT) Pihak Pertama',
            'first_signer_name' => 'Pejabat Pihak Pertama',
            'first_signer_position' => 'Jabatan Pihak Pertama',
            'second_signer_company' => '(PT) Pihak Kedua',
            'second_signer_name' => 'Pejabat Pihak Kedua',
            'second_signer_position' => 'Jabatan Pihak Kedua',
            'invoices' => [
                [
                    'index' => '1.',
                    'no' => 'xxxx-xxxx/xxxx/xxxx',
                    'date' => 'dd/mm/yyyy',
                    'amount' => 'Rp xxx.xxx,xx',
                    'vat_amount' => 'Rp xxx.xxx,xx',
                    'tax_amount' => 'Rp xxx.xxx,xx',
                    'backharge_amount' => 'Rp xxx.xxx,xx',
                    'total_amount' => 'Rp xxx.xxx,xx',
                ],
            ],
            'total_amount' => 'Rp xxx.xxx,xx',
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
