<?php

namespace Sanf\Core\Modules\Survey\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
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
            $imagePaths = '';
            foreach ($data['image_files'] as $image) {
                $imagePaths .= "/surveys/$dto->contractNo/{$data['code']}/{$image['file_name']}$delimiter";
            }

            $input = $data;
            $input['image_files'] = json_encode($data['image_files']);
            $input['image_path'] = $imagePaths;
            return $input;
        })->toArray();

        $this->repository->add($input);

        return true;
    }
}
