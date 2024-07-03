<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Dompdf\Dompdf;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Repositories\UserRepositoryInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Jobs\SendEmailPaymentAccelarationDocumentJob;

final class SendPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {

        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $pdfFile = $this->generateFile([
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

        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $pdfFile);

        $payload = (object) [
            'tempFile' => $tempFilePath,
            'company' => 'PIHAK PERTAMA (PT)',
            'bowheer' => 'PIHAK KEDUA (PT)',
            'plafondId' => 'PLAFOND NO',
        ];
        dispatch(new SendEmailPaymentAccelarationDocumentJob($user->username, $payload));

        return true;
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
