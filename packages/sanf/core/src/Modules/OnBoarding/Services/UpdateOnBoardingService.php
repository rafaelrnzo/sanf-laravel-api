<?php

namespace Sanf\Core\Modules\OnBoarding\Services;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\OnBoarding\Exceptions\OnBoardingNotFoundException;
use Sanf\Core\Modules\OnBoarding\Repositories\OnBoardingRepositoryInterface;
use Sanf\Core\Modules\OnBoarding\UpdateOnBoardingDto;

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

            $fileExist = Storage::exists("{$directory}{$filename}");
            if (!$fileExist) {
                throw new FileNotFoundException("{$directory}{$filename}");
            }

            $metadata = Storage::getMetaData("{$directory}{$filename}");
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
