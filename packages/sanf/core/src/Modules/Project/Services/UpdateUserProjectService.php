<?php


namespace Sanf\Core\Modules\Project\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\GeneralProjectException;

class UpdateUserProjectService extends ProjectService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->user_id != $user->id) {
            throw new GeneralProjectException('Project Not Found');
        }
        return $this->projectRepository->update([
            'id' => $project->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_file' => $dto->imageFile,
            'location_id' => $dto->locationId,
            'location_metadata' => $dto->locationMetadata,
            'phone_number' => $dto->phoneNumber,
            'whatsapp_number' => $dto->whatsappNumber,
            'business_email' => $dto->businessEmail,
            'submission_limit_at' => Carbon::createFromTimestamp($dto->submissionLimitAt),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        //TODO USER LOGGING USING EVENT
    }
}
