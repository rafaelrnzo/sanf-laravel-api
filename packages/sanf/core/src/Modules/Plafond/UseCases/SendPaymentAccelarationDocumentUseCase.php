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
