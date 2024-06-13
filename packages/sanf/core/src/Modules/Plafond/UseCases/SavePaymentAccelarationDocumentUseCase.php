<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

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
        $paymentAccDocumentEloquent = $this->paymentAccDocumentRepositoryInterface->create([
            'xid' => nano_id(),
            'client_id' => $dto->clientId,
            'plafond_id' => $dto->plafondId,
            'company' => $dto->companyName,
            'document_no' => $dto->documentNo,
            'document_date' => $dto->documentDate,
            'first_signer_name' => $dto->firstSigner->fullName,
            'first_signer_position' => $dto->firstSigner->position,
            'second_signer_name' => $dto->secondSigner->fullName,
            'second_signer_position' => $dto->secondSigner->position,
        ]);
        dd($paymentAccDocumentEloquent);
    }

    private function generateFile()
    {
    }
}
