<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Exceptions\PaymentAccDocumentNotFoundException;
use Sanf\Core\Modules\Plafond\Exceptions\PaymentAccDocumentRecipientNotFoundException;
use Sanf\Core\Modules\Plafond\Jobs\SendEmailPaymentAccelarationDocumentJob;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

final class SendPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
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

        $recipient = $paymentAccDocumentEloquent->bowheer_email;
        if (is_null($recipient) || empty($recipient)) {
            throw new PaymentAccDocumentRecipientNotFoundException();
        }

        $fileExist = Storage::exists($paymentAccDocumentEloquent->path);
        if (is_null($fileExist)) {
            throw new FileNotFoundException($paymentAccDocumentEloquent->path);
        }

        $payload = (object) [
            'origin' => $paymentAccDocumentEloquent->origin,
            'path' => $paymentAccDocumentEloquent->path,
            'company' => $paymentAccDocumentEloquent->company,
            'bowheer' => $paymentAccDocumentEloquent->bowher,
            'plafondId' => $paymentAccDocumentEloquent->plafond_id,
        ];
        dispatch(new SendEmailPaymentAccelarationDocumentJob($recipient, $payload));

        return true;
    }
}
