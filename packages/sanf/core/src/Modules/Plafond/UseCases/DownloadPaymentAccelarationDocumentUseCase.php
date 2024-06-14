<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Illuminate\Support\Facades\Storage;
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

        return Storage::download($paymentAccDocumentEloquent->path, $paymentAccDocumentEloquent->origin);
    }
}
