<?php

namespace Sanf\Core\Modules\Setting\Services;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dtos\UpdateOnBoardingDto;
use Sanf\Core\Modules\Setting\Exceptions\OnBoardingNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\OnBoardingRepositoryInterface;

class UpdateOnBoardingService implements ApplicationServiceInterface
{
    private OnBoardingRepositoryInterface $repository;

    public function __construct(OnBoardingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var UpdateOnBoardingDto $dto */
        $existingOnBoard = $this->repository->findById($dto->xid);
        if (!$existingOnBoard) {
            throw new OnBoardingNotFoundException();
        }

        if ($dto->imageFile) {
            $directory = config('image-path.on-board');

            $filename = file_upload($dto->imageFile, $directory, 'public');

            $fileExist = Storage::disk('minio_post')->exists("{$directory}{$filename}");
            if (!$fileExist) {
                throw new FileNotFoundException("{$directory}{$filename}");
            }

            $metadata = Storage::disk('minio_post')->getMetaData("{$directory}{$filename}");
            $fileMetadata = [
                'file_name' => $filename,
                'directory' => $directory,
                'path' => "{$directory}{$filename}",
                'mime_type' => $metadata['mimetype'],
                'size' => $metadata['size'],
            ];
        }

        $this->repository->update($existingOnBoard->id, [
            'title' => $dto->title ?? $existingOnBoard->title,
            'description' => $dto->description ?? $existingOnBoard->description,
            'image_file' => $fileMetadata ?? $existingOnBoard->image_file,
        ]);

        return true;
    }
}
