<?php


namespace Sanf\Core\Modules\Commodity\Services;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\Exceptions\GeneralCommodityException;

class UpdateCommodityByUserService extends CommodityByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $commodity = $this->commodityRepository->findByXid($dto->xid);
        if (is_null($commodity) || $commodity->user_id != $user->id) {
            throw new GeneralCommodityException('Commodity Not Found');
        }

        //TODO REFACTOR
        $imageFile = null;
        if ($dto->imageFile) {
            $newPath = config('image-path.commodity');
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

        $needApproval = in_array($commodity->status_id, [CommodityStatus::REJECTED, CommodityStatus::UNPUBLISHED], true);

        $updatedCommodity = $this->commodityRepository->update([
            'id' => $commodity->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_file' => $imageFile,
            'location_id' => $dto->locationId,
            'location_metadata' => $dto->locationMetadata,
            'phone_number' => $dto->phoneNumber,
            'whatsapp_number' => $dto->whatsappNumber,
            'business_email' => $dto->businessEmail,
            'status_id' => ($needApproval) ? CommodityStatus::WAITING_APPROVAL : $commodity->status_id,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new CommodityUpdatedEvent($commodity, $updatedCommodity));

        return $updatedCommodity;
    }
}
