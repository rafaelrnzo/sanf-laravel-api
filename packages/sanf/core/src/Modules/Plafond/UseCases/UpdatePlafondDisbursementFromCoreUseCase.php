<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementUpdateByCoreNotificationEvent;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementHasDoneException;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementIsNotDoneException;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Queries\ReadPlafondDisbursementByNoEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;

final class UpdatePlafondDisbursementFromCoreUseCase implements ApplicationServiceInterface
{
    private $disbursementRepository;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository
    ) {
        $this->disbursementRepository = $disbursementRepository;
    }

    /**
     * @return mixed
     */
    public function execute($formRequest = null)
    {
        $readRequest = new ReadPlafondDisbursementRequestDto([
            'disbursementXid' => $formRequest->disbursementNo,
            'plafondXid' => $formRequest->plafondNo,
            'profileXid' => $formRequest->custId,
        ]);

        $plafondDisbursements = $this->disbursementRepository->query(new ReadPlafondDisbursementByNoEloquentBuilder($readRequest));
        if (count($plafondDisbursements) === 0) {
            throw new PlafondDisbursementNotFoundException();
        }

        $disbursementData = $plafondDisbursements[0];
        if (in_array($disbursementData->status_id, [PlafondDisbursementStatusEnum::APPROVE, PlafondDisbursementStatusEnum::REJECT])) {
            throw new PlafondDisbursementHasDoneException();
        }
        if ($disbursementData->status_id !== PlafondDisbursementStatusEnum::DONE) {
            throw new PlafondDisbursementIsNotDoneException();
        }

        if ($formRequest->status === PlafondDisbursementStatusEnum::CORE_APPROVAL) {
            $disbursementStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::APPROVE));
            $disbursementSubmissionStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::APPROVE));
        } else {
            $disbursementStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::REJECT));
            $disbursementSubmissionStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::REJECT));
        }
        $version = $disbursementData->version + 1;

        $disbursementModel = $this->disbursementRepository->updateDisbursement($disbursementData->id, [
            'status_id' => $disbursementStatus->getValue(),
            'status' => $disbursementStatus->getLabel(),
            'updated_at' => date('Y-m-d H:i:s'),
            'version' => $version,
        ]);
        $submissionModel = $this->disbursementRepository->updateSubmission($disbursementData->disbursement_relation->id, [
            'status_id' => $disbursementSubmissionStatus->getValue(),
            'status' => $disbursementStatus->getLabel(),
            'updated_at' => date('Y-m-d H:i:s'),
            'version' => $version,
            'revision_notes' => $formRequest->notes ?? null,
            'user_updated_by' => json_encode([
                'source' => 'sanf-client',
            ]),
        ]);

        $notificationContent = (object) [
            'userId' => $disbursementModel->user_id ?? null,
            'clientId' => $disbursementModel->client_id,
            'client' => $disbursementModel->client_name,
            'bowheerId' => $disbursementModel->customer_bowheer_id,
            'bowheer' => $disbursementModel->customer_name,
            'plafondId' => $disbursementModel->plafond_id,
            'disbursementNo' => $disbursementModel->disbursement_no,
            'disbursementXid' => $disbursementModel->xid,
            'submissionXid' => $submissionModel->xid,
            'statusId' => $disbursementStatus->getValue(),
        ];

        event(new PlafondDisbursementUpdateByCoreNotificationEvent($notificationContent));

        return $plafondDisbursements;
    }
}
