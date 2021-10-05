<?php


namespace Sanf\Core\Modules\Project\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class UpdateUserProjectService extends ProjectByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->user_id != $user->id) {
            throw new GeneralProjectException('Project Not Found');
        }

        //TODO REFACTOR
        $imageFile = null;
        if ($dto->imageFile) {
            $newPath = config('image-path.project');
            $tempPath = config('image-path.temp');

            $exist = Storage::exists("{$newPath}{$dto->imageFile}");
            try {
                if (!$exist) {
                    Storage::move("{$tempPath}{$dto->imageFile}", "{$newPath}{$dto->imageFile}");
                }

                $metadata = Storage::getMetadata("{$newPath}{$dto->imageFile}");

                $imageFile = [
                    'file_name' => $dto->imageFile,
                    'directory' => $metadata['dirname'] ?? $newPath,
                    'path' => $metadata["path"],
                    'mime_type' => $metadata['mimetype'] ?? Storage::getMimeType("{$newPath}{$dto->imageFile}"),
                    'timestamp' => $metadata['timestamp'],
                    'size' => $metadata['size'],
                ];
            } catch (FileNotFoundException $exception) {
                report($exception);
            }
        }

        $isSendEmail = in_array($project->status_id, [ProjectStatus::REJECTED, ProjectStatus::UNPUBLISHED], true);

        $updatedProject = $this->projectRepository->update([
            'id' => $project->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_file' => $imageFile,
            'location_id' => $dto->locationId,
            'location_metadata' => $dto->locationMetadata,
            'phone_number' => $dto->phoneNumber,
            'whatsapp_number' => $dto->whatsappNumber,
            'business_email' => $dto->businessEmail,
            'submission_limit_at' => Carbon::createFromTimestamp($dto->submissionLimitAt),
            'status_id' => ($isSendEmail) ? ProjectStatus::WAITING_APPROVAL : $project->status_id,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        if ($isSendEmail) {
            event(new ProjectUpdatedEvent($project, $updatedProject));
        }

        return $updatedProject;
    }
}
