<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentSignDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentOTPNotFoundException;
use Sanf\Core\Modules\Contract\Exceptions\ESignUserNotRegisteredException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignDocumentSignAdInsService implements ApplicationServiceInterface
{
    private AdInsESignSignDocumentService $adInsSignDocumentService;
    private EloquentESignDocumentRepository $eSignRepository;

    public function __construct(AdInsESignSignDocumentService $adInsSignDocumentService, EloquentESignDocumentRepository $eSignRepository)
    {
        $this->adInsSignDocumentService = $adInsSignDocumentService;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param RequestESignDocumentSignDto $dto
     */
    public function execute($dto = null)
    {
        $userAdInsRecord = $this->eSignRepository->findUserBySanfId($dto->sanfId);
        if ($userAdInsRecord && $userAdInsRecord->status_id !== ESignRegistrationStatusEnum::COMPLETE) {
            throw new ESignUserNotRegisteredException();
        }

        $eSignDocument = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $eSignDocumentAssignee = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $dto->documentId);
        if (is_null($eSignDocumentAssignee) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $this->eSignRepository->updateDocumentAssignee($eSignDocumentAssignee->id, [
            'status_id' => ESignContractStatusEnum::DONE,
            'updated_at' => CarbonImmutable::now(),
        ]);

        $this->eSignRepository->updateDocument($eSignDocument->id, [
            'status_id' => ESignContractStatusEnum::ON_PROGRESS,
            'updated_at' => CarbonImmutable::now(),
        ]);

        $otpRecord = $this->eSignRepository->findOTPRequestBySanfIdWhereCodeIsNull($dto->sanfId);
        if (is_null($otpRecord) === true) {
            throw new ESignDocumentOTPNotFoundException();
        }
        $otpRecord = $this->eSignRepository->updateOTPRequest($otpRecord->id, [
            'code' => $dto->otp,
            'updated_at' => CarbonImmutable::now(),
        ]);
        $dto->password = $userAdInsRecord->code;

        return $this->adInsSignDocumentService->execute($dto);
    }
}
