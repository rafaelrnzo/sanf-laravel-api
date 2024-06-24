<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Exceptions\PaymentAccDocumentNotFoundException;
use Sanf\Core\Modules\Plafond\Jobs\SendEmailPaymentAccelarationDocumentJob;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

final class SendPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $coreClientRepository;
    private $paymentAccDocumentRepositoryInterface;

    public function __construct(
        PaymentAccelarationDocumentRepositoryInterface $paymentAccDocumentRepositoryInterface,
        ProfileRepositoryInterface $coreClientRepository
    ) {
        $this->coreClientRepository = $coreClientRepository;
        $this->paymentAccDocumentRepositoryInterface = $paymentAccDocumentRepositoryInterface;
    }

    public function execute($dto = null)
    {
        /** @var PlafondDisbursementFormRequest $formRequest */
        $userGuzzleEntity = $this->coreClientRepository->findById($dto->clientId);
        if (is_null($userGuzzleEntity)) {
            throw new ProfileNotFoundException("User {$dto->clientId} not found");
        }

        $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->findByPlafondId($dto->plafondId);

        if (is_null($paymentAccDocumentEloquent)) {
            throw new PaymentAccDocumentNotFoundException();
        }

        $pdfFile = $this->generateFile([
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

        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $pdfFile);

        $payload = (object) [
            'tempFile' => $tempFilePath,
            'company' => $paymentAccDocumentEloquent->company,
            'bowheer' => $paymentAccDocumentEloquent->bowher,
            'plafondId' => $paymentAccDocumentEloquent->plafond_id,
        ];
        dispatch(new SendEmailPaymentAccelarationDocumentJob($userGuzzleEntity->getEmail(), $payload));

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
