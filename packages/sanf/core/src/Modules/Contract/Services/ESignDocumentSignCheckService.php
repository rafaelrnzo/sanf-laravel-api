<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignDocumentSignCheckService implements ApplicationServiceInterface
{
    protected const UNSIGNED = 0;
    protected const SIGNED = 1;
    protected const FAILED = 2;
    protected const SIGN_IN = 3;

    protected AdInsESignDocumentSignCheckService $adInsDocumentSignCheckService;
    protected EloquentESignDocumentRepository $eSignRepository;

    public function __construct(
        AdInsESignDocumentSignCheckService $adInsDocumentSignCheckService,
        EloquentESignDocumentRepository $eSignRepository
    ) {
        $this->adInsDocumentSignCheckService = $adInsDocumentSignCheckService;
        $this->eSignRepository = $eSignRepository;
    }

    public function execute($dto = null)
    {
        $eSignDocument = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $eSignDocumentAssignment = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $dto->documentId);
        if (is_null($eSignDocumentAssignment) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $adInsDocumentSignResult = $this->adInsDocumentSignCheckService->execute(
            (object) [
                'documentId' => $eSignDocument->document_id,
                'referenceNo' => $eSignDocument->reference_no,
                'email' => $eSignDocumentAssignment->email,
            ]
        );

        if (is_null($adInsDocumentSignResult->statusSigning) === true) {
            return;
        }

        $totalAssignment = count($adInsDocumentSignResult->statusSigning->signer);
        $totalSignedDocument = 0;

        $statusSigning = array_map(
            function ($signer) use (&$totalSignedDocument) {
                switch ($signer->signStatus) {
                    case self::SIGNED:
                        $signer->signStatus = ESignContractStatusEnum::COMPLETED;
                        $totalSignedDocument++;
                        break;
                    case self::FAILED:
                        $signer->signStatus = ESignContractStatusEnum::FAILED;
                        break;
                    case self::UNSIGNED:
                    case self::SIGN_IN:
                    default:
                        $signer->signStatus = ESignContractStatusEnum::SUBMITTED;
                        break;
                }

                return (object) $signer;
            },
            $adInsDocumentSignResult->statusSigning->signer
        );

        $assigneFilterByEmail = array_filter(
            $statusSigning,
            function ($assigne) use ($eSignDocumentAssignment) {
                return strtolower($assigne->email) === $eSignDocumentAssignment->email;
            }
        );

        if (count($assigneFilterByEmail) === 0) {
            return $statusSigning;
        }

        $assigneStatus = array_values($assigneFilterByEmail)[0];

        $this->eSignRepository->updateDocumentAssignee(
            $eSignDocumentAssignment->id,
            [
                'status_id' => $assigneStatus->signStatus,
                'updated_at' => CarbonImmutable::now(),
            ]
        );

        if ($totalAssignment === $totalSignedDocument) {
            $this->eSignRepository->updateDocument(
                $eSignDocument->id,
                [
                'status_id' => $assigneStatus->signStatus,
                'updated_at' => CarbonImmutable::now(),
                ]
            );
        }

        return $statusSigning;
    }
}
