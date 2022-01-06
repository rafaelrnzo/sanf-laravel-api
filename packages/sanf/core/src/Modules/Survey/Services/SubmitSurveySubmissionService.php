<?php

namespace Sanf\Core\Modules\Survey\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\FormAddSurveyByUserDto;
use Sanf\Core\Modules\Survey\Repositories\SurveyRepositoryInterface;

class SubmitSurveySubmissionService implements ApplicationServiceInterface
{
    protected SurveyRepositoryInterface $repository;

    public function __construct(SurveyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param FormAddSurveyByUserDto|null $dto
     * @return bool
     * @throws BindingResolutionException
     */
    public function execute($dto = null): bool
    {
        $input = [];
        foreach ($dto->toArray() as $key => $value) {
            $input[Str::snake($key)] = $value;
        }
        $input['xid'] = nano_id();
        $input['items'] = collect($dto->items)->map(function ($data) use ($dto) {
            $delimiter = (count($data['image_files']) > 1) ? '|' : '';
            $imageFiles = null;
            $imagePaths = '';

            try {
                foreach ($data['image_files'] as $image) {
                    $newPath = config('image-path.survey');
                    $tempPath = config('image-path.temp');

                    $exist = Storage::exists("{$newPath}{$image}");
                    if (!$exist) {
                        Storage::move("{$tempPath}{$image}", "{$newPath}{$image}");
                    }

                    $metadata = Storage::getMetadata("{$newPath}{$image}");

                    $imageFiles[] = [
                        'file_name' => $image,
                        'directory' => $metadata['dirname'] ?? $newPath,
                        'path' => $metadata["path"],
                        'mime_type' => $metadata['mimetype'] ?? Storage::getMimeType("{$newPath}{$image}"),
                        'timestamp' => $metadata['timestamp'],
                        'size' => $metadata['size'],
                    ];

                    $imagePaths .= "{$newPath}$dto->contractNo/{$data['code']}/{$image}$delimiter";
                }
            } catch (FileNotFoundException $exception) {
                report($exception);
            }

            $input = $data;
            $input['image_files'] = json_encode($imageFiles);
            $input['image_path'] = $imagePaths;
            return $input;
        })->toArray();

        $this->repository->add($input);

        return true;
    }
}
