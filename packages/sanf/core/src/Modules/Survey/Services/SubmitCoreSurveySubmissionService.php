<?php

namespace Sanf\Core\Modules\Survey\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Entities\SurveyEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SubmitCoreSurveySubmissionService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(SanfCoreApiClient $internalApiClient)
    {
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param SurveyEntity $entity
     * @return bool
     */
    public function execute($entity = null)
    {
        $input = [
            'cust_id' => $entity->getProfileXid(),
            'reg_no' => $entity->getContractNo(),
            'br_id' => $entity->getBranchId(),
        ];
        $input['item'] = collect($entity->getItems())->map(function ($data) {
            $imagesFile = [];
            $paths = explode('|', $data['image_path']);
            foreach ($paths as $path) {
                $imagesFile[] = (object) [
                    'IMAGEITEM' => $path,
                ];
            }

            return (object) [
                'CODE' => $data['code'],
                'DESCRIPTION' => $data['title'],
                'NOTES' => $data['description'],
                'IMAGES' => $imagesFile,
            ];
        })->toArray();
        $this->internalApiClient->addSurvey($input);
    }
}
