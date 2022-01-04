<?php

namespace Sanf\Core\Modules\Survey\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\FormAddSurveyByUserDto;
use Sanf\Integration\InternalApiClient;

class SubmitCoreSurveySubmissionService implements ApplicationServiceInterface
{
    protected InternalApiClient $internalApiClient;

    public function __construct(InternalApiClient $internalApiClient)
    {
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param FormAddSurveyByUserDto|null $dto
     * @return bool
     */
    public function execute($dto = null)
    {
        $input = [
            'cust_id' => $dto->profileXid,
            'reg_no' => $dto->contractNo,
            'br_id' => $dto->branchId,
        ];
        $input['item'] = collect($dto->items)->map(function ($data) use ($dto) {
            $imagesFile = [];
            foreach ($data['image_files'] as $image) {
                $imagesFile[] = (object)[
                    'IMAGEITEM' => "/surveys/$dto->contractNo/{$data['code']}/{$image['file_name']}"
                ];
            }
            return (object)[
                'CODE' => $data['code'],
                'DESCRIPTION' => $data['title'],
                'NOTES' => $data['description'],
                'IMAGES' => $imagesFile
            ];
        })->toArray();

        $submit = $this->internalApiClient->addSurvey($input);

        return true;
    }
}
