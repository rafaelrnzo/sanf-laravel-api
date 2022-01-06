<?php

namespace Sanf\Core\Modules\Commodity\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Dto\CreateCommodityDto;
use Sanf\Core\Modules\Commodity\Events\CommodityCreatedEvent;

class CreateCommodityByUserService extends CommodityByUserService implements ApplicationServiceInterface
{

    /**
     * @param CreateCommodityDto|null $dto
     * @return mixed
     * @throws BindingResolutionException
     * @throws UserNotFoundException
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

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

        $commodity = $this->commodityRepository->add([
            'xid' => nano_id(),
            'user_id' => $user->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_file' => $imageFile,
            'location_id' => $dto->locationId,
            'location_metadata' => $dto->locationMetadata,
            'phone_number' => $dto->phoneNumber,
            'whatsapp_number' => $dto->whatsappNumber,
            'business_email' => $dto->businessEmail,
            'status_id' => CommodityStatus::WAITING_APPROVAL,
//            'modified_by' => //TODO USER SNAPSHOT
            'city_name' => $dto->locationMetadata['city_name'],
            'province_name' => $dto->locationMetadata['province_name'],
            'image_path' => $imageFile['path']
        ]);

        event(new CommodityCreatedEvent($commodity));

        return $commodity;
    }
}
