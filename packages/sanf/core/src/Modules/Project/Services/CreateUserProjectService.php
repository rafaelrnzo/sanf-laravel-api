<?php


namespace Sanf\Core\Modules\Project\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\ProjectStatus;

class CreateUserProjectService extends ProjectService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        return $this->projectRepository->add([
            'xid' => nano_id(),
            'user_id' => $user->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_file' => $dto->imageFile,
            'location_id' => $dto->locationId,
            'location_metadata' => $dto->locationMetadata,
            'phone_number' => $dto->phoneNumber,
            'whatsapp_number' => $dto->whatsappNumber,
            'business_email' => $dto->businessEmail,
            'submission_limit_at' => Carbon::createFromTimestamp($dto->submissionLimitAt),
            'status_id' => ProjectStatus::WAITING_APPROVAL,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        //TODO USER LOGGING USING EVENT
    }
}
