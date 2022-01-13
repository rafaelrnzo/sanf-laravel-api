<?php

namespace Sanf\Core\Modules\Survey\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDTO;
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
     * @param AddSurveySubmissionRequestDTO|null $dto
     * @return bool
     * @throws BindingResolutionException
     */
    public function execute($dto = null)
    {
        $input = [];
        foreach ($dto->toArray() as $key => $value) {
            $input[Str::snake($key)] = $value;
        }
        $input['xid'] = nano_id();
        $input['items'] = collect($dto->items)->map(function ($data) use ($dto) {
            $imageFiles = null;
            $imagePaths = null;

            try {
                $path = config('image-path.survey');
                foreach ($data['image_files'] as $image) {
                    $filename = file_upload($image, $path, 'public');

                    $exist = Storage::exists($path . $filename);
                    throw_if(!$exist, new FileNotFoundException($path));

                    $imageFiles[] = [
                        'file_name' => $filename,
                        'directory' => $path,
                        'path' => $path . $filename,
                        'mime_type' => $image->getClientMimeType(),
                        'size' => $image->getSize(),
                    ];

                    $imagePaths[] = $path . "$dto->contractNo/{$data['code']}/" . $filename;
                }
            } catch (FileNotFoundException $exception) {
                report($exception);
            }

            $input = $data;
            $input['image_files'] = json_encode($imageFiles);
            $input['image_path'] = implode("|", $imagePaths);
            return $input;
        })->toArray();

        $surveySubmission = $this->repository->add($input);

        dispatch(new SubmitCoreSurveySubmissionJob($surveySubmission));

        return true;
    }
}
