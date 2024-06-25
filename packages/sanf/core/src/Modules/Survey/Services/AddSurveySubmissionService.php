<?php

namespace Sanf\Core\Modules\Survey\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDto;
use Sanf\Core\Modules\Survey\Jobs\SubmitCoreSurveySubmissionJob;
use Sanf\Core\Modules\Survey\Repositories\SurveyRepositoryInterface;

class AddSurveySubmissionService implements ApplicationServiceInterface
{
    protected SurveyRepositoryInterface $repository;

    public function __construct(SurveyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AddSurveySubmissionRequestDto|null $dto
     * @return bool
     * @throws BindingResolutionException
     */
    public function execute($dto = null)
    {
        $inputs = [];
        foreach ($dto->toArray() as $key => $value) {
            $inputs[Str::snake($key)] = $value;
        }
        $inputs['xid'] = nano_id();
        $inputs['items'] = collect($dto->items)->map(function ($data) use ($dto) {
            $imageFiles = null;
            $imagePaths = null;

            try {
                $tempDir = config('image-path.temp');
                $dir = config('image-path.survey');
                foreach ($data['image_files'] as $fileName) {
                    $path = $dir . "$dto->contractNo/{$data['code']}/" . $fileName;

                    $exist = Storage::disk('minio_post')->exists($tempDir . $fileName);
                    if ($exist) {
                        Storage::disk('minio_post')->move($tempDir . $fileName, $path);
                    }

                    $metadata = Storage::disk('minio_post')->getMetaData($path);
                    $imageFiles[] = [
                        'file_name' => $fileName,
                        'directory' => $dir,
                        'path' => $path,
                        'mime_type' => $metadata['mimetype'],
                        'size' => $metadata['size'],
                    ];

                    $imagePaths[] = $path;
                }
            } catch (FileNotFoundException $exception) {
                report($exception);
            }

            $input = $data;
            $input['image_files'] = json_encode($imageFiles);
            $input['image_path'] = implode('|', $imagePaths);

            return $input;
        })->toArray();

        $surveySubmission = $this->repository->add($inputs);

        dispatch(new SubmitCoreSurveySubmissionJob($surveySubmission));
    }
}
