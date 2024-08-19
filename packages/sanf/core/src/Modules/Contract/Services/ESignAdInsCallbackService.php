<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use Exception;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\AdInsCallbackTypeEnum;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterMailEvent;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterNotificationEvent;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignCompleteNotificationEvent;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;

final class ESignAdInsCallbackService implements ApplicationServiceInterface
{
    protected EloquentESignDocumentRepository $eSignDocumentRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;

    public function __construct(
        EloquentESignDocumentRepository $eSignDocumentRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory
    ) {
        $this->eSignDocumentRepository = $eSignDocumentRepository;
        $this->eSignDocumentSpecificationFactory = $eSignDocumentSpecificationFactory;
    }

    public function execute($dto = null)
    {
        switch ($dto->callbackType) {
            case AdInsCallbackTypeEnum::ACTIVATION_COMPLETE:
                $this->activationComplete($dto);
                break;
            case AdInsCallbackTypeEnum::SIGNING_COMPLETE:
                $this->signDocument($dto);
                break;
            case AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE:
                $this->completeSignDocument($dto);
                break;
        }

        return true;
    }

    protected function activationComplete(object $dto)
    {
        try {
            $adInsUser = $this->eSignDocumentRepository->findUserByEmail($dto->email);
            if ($adInsUser) {
                if ($adInsUser->status_id !== ESignRegistrationStatusEnum::COMPLETE) {
                    $this->eSignDocumentRepository->updateUser($adInsUser->id, [
                        'status_id' => ESignRegistrationStatusEnum::COMPLETE,
                        'updated_at' => CarbonImmutable::now(),
                    ]);
                }

                $bodyEmail = (object) [
                    'email' => $adInsUser->email,
                    'name' => $adInsUser->full_name,
                ];
                event(new ESignAdsInsRegisterMailEvent($bodyEmail));
                event(new ESignAdsInsRegisterNotificationEvent($adInsUser->userId));
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return true;
    }

    protected function signDocument(object $dto)
    {
        try {
            $eSignDocument = $this->eSignDocumentRepository->findDocumentByDocId($dto->documentId);
            if (is_null($eSignDocument) === true) {
                throw new ESignDocumentNotFoundException();
            }

            $assigmentsDocument = $this->eSignDocumentRepository->documentAssigneeQuery(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByDocId($eSignDocument->document_id, null, null)
            );

            $assignmentFilterByEmail = array_filter($assigmentsDocument, function ($eSignDocumentAssignment) use ($dto) {
                return $eSignDocumentAssignment->email === $dto->email;
            });

            if (count($assignmentFilterByEmail) === 0) {
                throw new ESignDocumentNotFoundException();
            }

            $eSignDocumentAssignment = $assignmentFilterByEmail[0];
            $this->eSignDocumentRepository->updateDocumentAssignee($eSignDocumentAssignment->id, [
                'status_id' => ESignContractStatusEnum::DONE,
                'updated_at' => CarbonImmutable::now(),
            ]);
            $this->eSignDocumentRepository->updateDocument($eSignDocument->id, [
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'updated_at' => CarbonImmutable::now(),
            ]);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    protected function completeSignDocument(object $dto)
    {
        try {
            $eSignDocument = $this->eSignDocumentRepository->findDocumentByDocId($dto->documentId);
            if (is_null($eSignDocument) === true) {
                throw new ESignDocumentNotFoundException();
            }

            $this->eSignDocumentRepository->updateDocument($eSignDocument->id, [
                'status_id' => ESignContractStatusEnum::COMPLETED,
                'updated_at' => CarbonImmutable::now(),
            ]);

            $assigmentsDocument = $this->eSignDocumentRepository->documentAssigneeQuery(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByDocId($eSignDocument->document_id, null, null)
            );

            foreach ($assigmentsDocument as $eSignDocumentAssignment) {
                event(new ESignDocumentSignCompleteNotificationEvent($eSignDocumentAssignment->user_id, $eSignDocument->document_name));
            }
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
